<?php

namespace App\Services\Api;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Models\Notification as NotificationModel;
use Kreait\Firebase\Exception\MessagingException;

class NotificationService
{

    public function index()
    {
        return auth()->user()->notifications;
    }


    public function send($user, $title, $message, $type = 'basic')
    {
        // Path to the service account key JSON file
        $serviceAccountPath = storage_path('app/yara-a6774-firebase-adminsdk-c5t45-d47a020368.json');

        // Initialize the Firebase Factory with the service account
        $factory = (new Factory)->withServiceAccount($serviceAccountPath);

        // Create the Messaging instance
        $messaging = $factory->createMessaging();

        // Prepare the notification array
        $notification = [
            'title' => $title,
            'body' => $message,
            'sound' => 'default',
        ];

        // Additional data payload
        $data = [
            'type' => $type,
            'id' => $user['id'],
            'message' => $message,
        ];

        // Create the CloudMessage instance
        $cloudMessage = CloudMessage::withTarget('token', $user['fcm_token'])
            ->withNotification($notification)
            ->withData($data);

        try {
            // Send the notification
            $messaging->send($cloudMessage);

            // Save the notification to the database
            NotificationModel::query()->create([
                'type' => 'App\Notifications\UserFollow',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user['id'],
                'data' => json_encode([
                    'user' => $user['first_name'] . ' ' . $user['last_name'],
                    'message' => $message,
                    'title' => $title,
                ]), // The data of the notification
            ]);
            return 1;
        } catch (MessagingException $e) {
            Log::error($e->getMessage());
            return 0;
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            Log::error($e->getMessage());
            return 0;
        }
    }

    public function markAsRead($notificationId): bool
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);

        if(isset($notification)) {
            $notification->markAsRead();
            return true;
        }else return false;
    }

    public function destroy($id): bool
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        if(isset($notification)) {
            $notification->delete();
            return true;
        }else return false;
    }

}
