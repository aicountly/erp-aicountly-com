<?php
return [
   'errors' => [
        'exists'  => '{item} already exists.',        
    ]    
];

/* return [
    'errors' => [
        'required'  => 'The {field} field is required.',
        'not_found' => '{item} not found.',
        'server'    => 'Internal server error.',
    ],

    'form' => [
        'submit'  => 'Submit',
        'cancel'  => 'Cancel',
        'reset'   => 'Reset',
    ],

    'messages' => [
        'welcome'      => 'Welcome, {name}!',
        'logout_success' => 'You have been logged out successfully.',
        'save_success'   => 'Data saved successfully!',
    ],

    'buttons' => [
        'edit'   => 'Edit',
        'delete' => 'Delete',
        'back'   => 'Back',
    ],
];
// Access error message
echo lang('AppLang.errors.required');       // Output: The {field} field is required.

// Access button label
echo lang('AppLang.buttons.edit');          // Output: Edit

// With placeholders
echo lang('AppLang.errors.not_found', ['item' => 'User']);  // Output: User not found.

echo lang('AppLang.messages.welcome', ['name' => 'Raj']);   // Output: Welcome, Raj!

 */