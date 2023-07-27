<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Helpers\Helper;
use App\Http\Requests\GroupControllerRequests\GroupDestroyRequest;
use App\Http\Requests\GroupControllerRequests\GroupIndexRequest;
use App\Http\Requests\GroupControllerRequests\GroupFilterRequest;
use App\Http\Requests\GroupControllerRequests\GroupSearchRequest;
use App\Http\Requests\GroupControllerRequests\GroupShowRequest;
use App\Http\Requests\GroupControllerRequests\GroupMeRequest;
use App\Http\Requests\GroupControllerRequests\GroupStoreRequest;
use App\Http\Requests\GroupControllerRequests\GroupUpdateRequest;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->middleware('team:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(GroupIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all contact with all their relations
            $contacts = Group::with(['contacts'])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted contact with all their relations
            $contacts = Group::onlyTrashed()->with(['contacts'])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all contact with out their relations
            $contacts = Group::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($contacts) {

            if (count($contacts) > 0) {
                return $this->success($contacts);
            } else {
               return $this->noContent('Groups were not found');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterIndex(GroupFilterRequest $request)
    {
        $user_id = is_null($request->input('user_id'))? false : Helper::escapeForLikeColumnQuery($request->input('user_id'));
        $title = is_null($request->input('title'))? false : Helper::escapeForLikeColumnQuery($request->input('title'));
        $contacts = is_null($request->input('contacts'))? false : $request->input('contacts');
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $contacts = Group::when($user_id, function ($query, $user_id) {
            return $query->where('user_id', $user_id);

        })->when($title, function ($query, $title) {
            return $query->where('title', 'like', '%'.$title.'%');

        })->when($contacts, function ($query, $contacts) {

            foreach($contacts as $contact_attribute) {
                $query->whereJsonContains('contacts', $contact_attribute);
            }
            return $query;

        })->when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        });

        // Check if the builder has any where clause
        if (count($contacts->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $contacts = $contacts->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $contacts = $contacts->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $contacts = $contacts->take(1000)->get();
        }

        // Return success
        if ($contacts) {
            if (count($contacts) > 0) {
                return $this->success($contacts);
            } else {
               return $this->noContent('No contact was found for this range');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchIndex(GroupSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));
        $search_date = is_null($request->input('search'))? false : Helper::stringToCarbonDate($request->input('search'));

        // Build search query
        $contacts = Group::when($search_string, function ($query) use ($request, $search_string, $search_date) {

            return $query->when($request->input('user_id'), function($query) use ($request) {

                return $query->where('user_id', $request->input('user_id'));

            })->where(function ($query) use ($search_string, $search_date) {

                return $query->where('id', 'like', '%'.$search_string.'%')
                ->orWhere('title', 'like', '%'.$search_string.'%')
                ->orWhereJsonContains('contacts', $search_string)
                ->when($search_date, function ($query, $search_date) {
                    return $query->orWhere('created_at', '=', $search_date);
                });
            });
        });

        // Check if the builder has any where clause
        if (count($contacts->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $contacts = $contacts->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($contacts) {
            if (count($contacts) > 0) {
                return $this->success($contacts);
            } else {
               return $this->noContent('No contact was found for this range');
            }
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GroupStoreRequest $request)
    {
        // Fill the contact model
        $contact = new Group;
        $contact = $contact->fill($request->toArray());

        // Additional params
        $contact->user_id = auth()->user()->id;

        // Return success
        if ($contact->save()) {
            return $this->entityCreated($contact,'Group was saved');
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(GroupShowRequest $request)
    {
        // Use contact model passed in from request authorization
        $contact = $request->contact;

        // Return success
        if ($contact) {

            if ($request->input('properties')) {
                $contact = $contact->load('contacts');
            }

            return $this->success($contact);
        } else {
            // Return Failure
            return $this->notFound();
        }
    }

    /**
     * Display the authenticated user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(GroupMeRequest $request)
    {
        // Get a user groups
        $contacts = Group::where('user_id', auth()->user()->id)
        ->orderBy('created_at', 'desc')
        ->take(1000)
        ->paginate(25);

        // Return success
        if ($contacts) {
            return $this->success($contacts);
        } else {
            // Return Failure
            return $this->notFound();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(GroupUpdateRequest $request)
    {
        // Use contact model passed in from request authorization
        $contact = $request->contact;

        if ($contact) {

            // Fill requestor input
            $contact->fill($request->toArray());

            // Update contact
            if ($contact->update()) {
                return $this->actionSuccess('Group was updated');
            } else {
                return $this->unavailableService();
            }
        } else {
            // Return failure
            return $this->notFound();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(GroupDestroyRequest $request)
    {
        // Use contact model passed in from request authorization
        $contact = $request->contact;

        if ($contact) {

            // Delete contact
            if ($contact->delete()) {
                return $this->actionSuccess('Group was deleted');
            } else {
                return $this->unavailableService();
            }
        } else {
            // Return failure
            return $this->notFound();
        }
    }

    /**
     * Validate existence of resource pool.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        $test = Group::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
