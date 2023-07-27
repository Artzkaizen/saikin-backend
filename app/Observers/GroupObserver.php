<?php

namespace App\Observers;

use App\Models\Group;
use App\Models\Contact;

class GroupObserver
{
   /**
     * Handle the app models group "created" event.
     *
     * @param  \App\Models\Group  $group
     * @return void
     */
    public function created(Group $group)
    {
        if (!empty($group->contacts)) {

            // Check if the given contacts are valid
            $contacts = $group->contacts ?? [];
            $contacts = Contact::where('user_id',$group->user_id)->whereIn('id', $contacts)->get();

            // Sync valid group contacts
            $group->contacts()->sync($contacts);

            // Update group contacts
            Group::withoutEvents(function () use ($group, $contacts) { 
                Group::where('id',$group->id)->update(['contacts'=>$contacts->pluck('id')->toArray()]);
            });
        }
    }

    /**
     * Handle the app models group "updated" event.
     *
     * @param  \App\Models\Group  $group
     * @return void
     */
    public function updated(Group $group)
    {
        if ($group->wasChanged('contacts')) {

            // Check if the given contacts are valid
            $contacts = $group->contacts ?? [];
            $contacts = Contact::where('user_id',$group->user_id)->whereIn('id', $contacts)->get();

            // Sync valid group contacts
            $group->contacts()->sync($contacts);

            // Update group contacts
            Group::withoutEvents(function () use ($group, $contacts) { 
                Group::where('id',$group->id)->update(['contacts'=>$contacts->pluck('id')->toArray()]);
            });
        }
    }

    /**
     * Handle the app models group "deleted" event.
     *
     * @param  \App\Models\Group  $group
     * @return void
     */
    public function deleted(Group $group)
    {
        // Detach all contacts from the group
        $group->contacts()->detach();
    }

    /**
     * Handle the app models group "restored" event.
     *
     * @param  \App\Models\Group  $group
     * @return void
     */
    public function restored(Group $group)
    {
        // Check if the previous contacts are still valid
        $contacts = $group->contacts ?? [];
        $contacts = Contact::where('user_id',$group->user_id)->whereIn('id', $contacts)->get();

        // Attach valid contacts to the group
        $group->contacts()->attach($contacts);

        // Update group contacts
        Group::withoutEvents(function () use ($group, $contacts) { 
            Group::where('id',$group->id)->update(['contacts'=>$contacts->pluck('id')->toArray()]);
        });
    }

    /**
     * Handle the app models group "force deleted" event.
     *
     * @param  \App\Models\Group  $group
     * @return void
     */
    public function forceDeleted(Group $group)
    {
        // Detach all contacts from the group
        $group->contacts()->detach();
    }
}
