<h1>User Detail</h1>

<h3>
    Name: {{ $user['name'] }} |
    City: {{ !empty($user['city']) ? $user['city'] : 'N/A' }} |
    Phone: {{ !empty($user['phone']) ? $user['phone'] : 'N/A' }}
</h3>