<?php

return [
    'session_expired_message' => 'Вашата сесия е изтекла поради продължителна неактивност. Моля, влезте отново.',

    'signing_api_url' => env('VIDEO_SIGNING_API_URL','test@api.com'),
    'period_minutes'  => env('VIDEO_SIGNING_PERIOD', 10),
];
