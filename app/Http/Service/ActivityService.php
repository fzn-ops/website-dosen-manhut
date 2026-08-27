<?php

namespace App\Http\Service;

use App\Models\Activity;
use App\Models\ActivityPicture;
use Illuminate\Support\Facades\Storage;


class ActivityService
{

    // Tambahkan fungsi ini di ActivityService.php
    public function getActivityById($activityId)
    {
        return Activity::with(['user', 'pictures'])->findOrFail($activityId);
    }

    public function getAllActivitiesPaginated($keyword = null, $perPage = 9)
    {
        $query = Activity::with(['user', 'primaryPicture']);

        if ($keyword) {
            $query->where('activity_name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
        }

        return $query->orderBy('activity_date_start', 'desc')
                     ->paginate($perPage);
    }

    public function getRandomActivities($excludeActivityId, $limit = 3)
    {
        return Activity::with(['user', 'primaryPicture'])
            ->where('id', '!=', $excludeActivityId)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function getActivitiesByUserIdPaginated($userId, $searchKeyword = null, $perPage = 5)
    {
        $query = Activity::with('pictures') 
                         ->where('user_id', $userId);

        if ($searchKeyword) {
            $query->where(function($q) use ($searchKeyword) {
                $q->where('activity_name', 'like', "%{$searchKeyword}%")
                  ->orWhere('description', 'like', "%{$searchKeyword}%");
            });
        }
        
        return $query->orderBy('activity_date_start', 'desc')
                     ->paginate($perPage);
    }

    public function addActivity($userId, array $data, $imageFiles = [])
    {
        $activity = Activity::create([
            'user_id'             => $userId,
            'activity_name'       => $data['activity_name'],
            'description'         => $data['description'],
            'job'                 => $data['job'],
            'activity_type'       => $data['activity_type'],
            'quote'               => $data['quote'],
            'activity_date_start' => $data['activity_date_start'],
            'activity_date_end'   => $data['activity_date_end'] ?? null,
        ]);

        if (!empty($imageFiles)) {
            $isFirst = true; 

            foreach ($imageFiles as $file) {
                $path = $file->store('activities', 'public');
                
                ActivityPicture::create([
                    'activity_id' => $activity->id,
                    'path' => $path, 
                    'is_primary'  => $isFirst
                ]);
                
                $isFirst = false; 
            }
        }

        return $activity;
    }
}