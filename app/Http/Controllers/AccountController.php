<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use App\Helpers\Helper;
use App\Helpers\MediaImages;
use App\Http\Requests\AccountControllerRequests\AccountDestroyRequest;
use App\Http\Requests\AccountControllerRequests\AccountFilterRequest;
use App\Http\Requests\AccountControllerRequests\AccountIndexRequest;
use App\Http\Requests\AccountControllerRequests\AccountMeRequest;
use App\Http\Requests\AccountControllerRequests\AccountSearchRequest;
use App\Http\Requests\AccountControllerRequests\AccountShowRequest;
use App\Http\Requests\AccountControllerRequests\AccountStoreRequest;
use App\Http\Requests\AccountControllerRequests\AccountUpdateRequest;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('team:api')->except('store','me','update','destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(AccountIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all accounts with all their relations
            $accounts = Account::with([
                'user',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted accounts with all their relations
            $accounts = Account::onlyTrashed()->with([
                'user',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all accounts with out their relations
            $accounts = Account::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($accounts) {
            
            if (count($accounts) > 0) {
                return $this->success($accounts);
            } else {
               return $this->noContent('No account was found');
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
    public function filterIndex(AccountFilterRequest $request)
    {
        $user_id = is_null($request->input('user_id'))? false : Helper::escapeForLikeColumnQuery($request->input('user_id'));
        $phone = is_null($request->input('phone'))? false : Helper::escapeForLikeColumnQuery($request->input('phone'));
        $first_name = is_null($request->input('first_name'))? false : Helper::escapeForLikeColumnQuery($request->input('first_name'));
        $last_name = is_null($request->input('last_name'))? false : Helper::escapeForLikeColumnQuery($request->input('last_name'));
        $verified = is_null($request->input('verified'))? false : Helper::escapeForLikeColumnQuery($request->input('verified'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $accounts = Account::when($user_id, function ($query, $user_id) {
            return $query->where('user_id', 'like', '%'.$user_id.'%');
        })->when($phone, function ($query, $phone) {
            return $query->where('phone', 'like', '%'.$phone.'%');
        })->when($first_name, function ($query, $first_name) {
            return $query->where('first_name', 'like', '%'.$first_name.'%');
        })->when($last_name, function ($query, $last_name) {
            return $query->where('last_name', 'like', '%'.$last_name.'%');
        })->when($verified, function ($query, $verified) {
            return $query->where('verified', 'like', '%'.$verified.'%');
        });

        // Check if the builder has any where clause
        if (count($accounts->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $accounts = $accounts->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $accounts = $accounts->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $accounts = $accounts->take(1000)->get();
        }

        // Return success
        if ($accounts) {
            if (count($accounts) > 0) {
                return $this->success($accounts);
            } else {
               return $this->noContent('No account was found for this range');
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
    public function searchIndex(AccountSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));

        // Build search query
        $accounts = Account::when($search_string, function ($query, $search_string) {
            return $query->where('user_id', 'like', '%'.$search_string.'%')
            ->orWhere('phone', 'like', '%'.$search_string.'%')
            ->orWhere('first_name', 'like', '%'.$search_string.'%')
            ->orWhere('last_name', 'like', '%'.$search_string.'%')
            ->orWhere('verified', 'like', '%'.$search_string.'%');
        });

        // Check if the builder has any where clause
        if (count($accounts->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $accounts = $accounts->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($accounts) {
            if (count($accounts) > 0) {
                return $this->success($accounts);
            } else {
               return $this->noContent('No account was found for this range');
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
    public function store(AccountStoreRequest $request)
    {
        // Check if user has an existing account and validate user
        $accounts = Account::where('user_id',auth()->user()->id)->get();
        if ($accounts > 9) {
            return $this->requestConflict('Account limit exceeded, please delete or edit existing addresses');
        }

        // Fill the user account model
        $account = new Account;
        $account = $account->fill($request->toArray());

        // Additional params
        $account->user_id = auth()->user()->id;

        // Store new images to server or cloud service
        $stored_images = MediaImages::images($request->file('photos'))
        ->base64Images($request->input('base64_photos'))
        ->imageUrls($request->input('url_photos'))
        ->path('public/images/account')->limit(1)->store()->pluck('image_url');

        // Add images to model
        $account->picture = $stored_images->isNotEmpty() ? $stored_images->first() : $account->picture;

        // Return success
        if ($account->save()) {
            return $this->entityCreated($account,'User account was saved');
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
    public function show(AccountShowRequest $request)
    {
        // Use account model passed in from request authorization
        $account = $request->account;

        // Return success
        if ($account) {

            if ($request->input('properties')) {
                $account = $account->load('user');
            }

            return $this->success($account);
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
    public function me(AccountMeRequest $request)
    {
        // Get a single user account
        $accounts = Account::when($request->input('properties'),function ($query) { return $query->with('user'); })
        ->where('user_id',auth()->user()->id)
        ->get();

        // Return success
        if ($accounts) {
            return $this->success($accounts);
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
    public function update(AccountUpdateRequest $request)
    {
        // Use account model passed in from request authorization
        $account = $request->account;

        if ($account) {

            // Fill requestor input
            $account->fill($request->toArray());

            // Store new images to server or cloud service
            $stored_images = MediaImages::images($request->file('photos'))
            ->base64Images($request->input('base64_photos'))
            ->imageUrls($request->input('url_photos'))
            ->path('public/images/account')->limit(1)->replace([$account->picture])->pluck('image_url');

            // Add images to model
            $account->picture = $stored_images->isNotEmpty() ? $stored_images->first() : $account->picture;

            // Un-verify user account if sensitive details are changed
            if ($account->isDirty('phone')) {
                $account->verified = false;
            }

            // Update user account
            if ($account->update()) {
                return $this->actionSuccess('User account was updated');
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
    public function destroy(AccountDestroyRequest $request)
    {
        // Use account model passed in from request authorization
        $account = $request->account;

        if ($account) {

            // Delete user account
            if ($account->delete()) {
                return $this->actionSuccess('User Account was deleted');
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
        $test = Account::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}