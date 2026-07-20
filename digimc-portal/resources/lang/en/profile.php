<?php

return [
    'profile' => 'Profile',

    'common' => [
        'hello_name' => 'Hello, :name!',
        'view_profile' => 'View your profile',
        'regards' => 'Best regards,',
        'team' => ':app Team',
    ],

    'password_changed' => [
        'subject' => 'Your password was updated on :app',
        'title' => 'Password changed',
        'intro' => 'You have successfully changed your password.',
        'if_not_you' => 'If this wasn’t you, please contact support immediately.',
    ],

    'profile_updated' => [
        'fields' => [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'profile_image_path' => 'Profile photo',
            'wants_notifications' => 'Email notifications',
            'subscribed_news' => 'News subscription',
            'subscribed_weekly' => 'Weekly newsletter',
        ],

        'subject' => 'Your profile was updated on :app',
        'title' => 'Your profile was updated',
        'intro' => 'We’ve detected changes to your profile.',
        'no_changes' => 'No visible changes were detected, but an update was attempted.',
        'changed_header' => 'Here’s what changed:',
        'photo_updated' => 'Your profile photo was updated.',
        'changed_item' => '<strong>:label</strong>: :old → :new',
        'security_note' => 'If this wasn’t you, please change your password and contact support.',

        'updated_on' => 'Updated on',
    ],


    'activation' => [
        'title' => 'Account Activation',
        'hello_name' => 'Hello, :name!',
        'intro' => 'Thank you for registering on our portal. To activate your account, please click the button below.',
        'button' => 'Activate Account',
        'copy_link' => 'Or copy the following link into your browser:',
        'note_title' => 'Important:',
        'note_text' => 'This link will be valid for 30 minutes.',
        'help_title' => 'If you have problems:',
        'help_spam' => 'Check your Spam or Junk folder.',
        'help_expired' => 'If the link has expired, you can request a new one from the login page.',
        'help_contact' => 'Contact us for assistance.',
        'thanks' => 'Thank you,',
        'team' => 'The :app Team',
        'alert_title' => 'Success!',
        'alert_message' => 'Please check your email for the activation link.',
    ],

    'ui' => [
        'browse' => 'Browse…',
        'no_file_selected' => 'No file selected.',
    ],
    'hints' => [
        'photo_rules' => 'JPG/PNG/WebP, up to 2MB.',
    ],

    'info' => 'Information',
    'security' => 'Security',
    'collection' => 'Collection',
    'favorite' => 'Favorites',

    'title_show' => 'My Profile',
    'title_edit' => 'Update information',
    'title_pass' => 'Change password',

    'yes' => 'Yes',
    'no' => 'No',

    'buttons' => [
        'edit' => 'Edit',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'confirm' => 'Confirm & Save',
        'change_pw' => 'Change password',
    ],

    'flash' => [
        'profile_updated' => 'Profile updated.',
        'password_updated' => 'Password updated.',
        'general_error' => 'We couldn’t complete your request right now. Please try again.',
        'password_change_error' => 'We couldn’t change your password right now. Please try again.',
    ],

    'fields' => [
        'email' => 'Email address',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'profile_photo' => 'Profile photo',
        'current_password' => 'Current password',
        'new_password' => 'New password',
        'password_confirmation' => 'Confirm new password',
        'wants_notifications' => 'Receive email notifications',
        'subscribed_news' => 'News subscription',
        'subscribed_weekly' => 'Weekly newsletter subscription',
        'no_photo' => 'No photo',
    ],

    'modal' => [
        'title' => 'Confirm changes',
        'prompt' => 'Please enter your current password to save your changes.',
        'error' => 'Password is required.',
    ],

    'cancel_modal' => [
        'title' => 'Discard changes?',
        'prompt' => 'You have unsaved changes. Do you want to discard them and go back?',
        'discard' => 'Discard changes',
        'keep_editing' => 'Keep editing',
    ],

    'confirm_discard' => 'Discard all changes and go back?',

    'required' => [
        'current_password' => 'The Current password field is required.',
        'password' => 'The New password field is required.',
        'password_confirmation' => 'The Confirm new password field is required.',
        'confirmed' => 'The Confirmation does not match.',
    ],
];
