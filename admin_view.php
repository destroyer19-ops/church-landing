<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

app_require_admin_auth();

try {
    $connection = app_db();
    app_ensure_analytics_tables($connection);

    $recentViewersResult = $connection->query('SELECT name, email, phone, country, created_at FROM viewers ORDER BY created_at DESC LIMIT 50');
    $sessionsResult = $connection->query(
        'SELECT vs.id, v.name, v.email, vs.number_of_viewers, vs.viewed_at
        FROM viewing_sessions vs
        JOIN viewers v ON vs.viewer_id = v.id
        ORDER BY vs.viewed_at DESC
        LIMIT 100'
    );

    $connection->close();
} catch (Throwable $exception) {
    http_response_code(500);
    echo 'The analytics dashboard is unavailable because the database connection failed.';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Stream Viewers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-6xl mx-auto">
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-8" role="alert">
            <p class="font-bold">Protected Dashboard</p>
            <p>This page is protected with HTTP basic authentication using the configured admin credentials.</p>
        </div>

        <h1 class="text-3xl font-bold mb-6">Stream Viewer Analytics</h1>

        <!-- Recent Registrations -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <h2 class="text-2xl font-bold mb-4">Recent Registrations</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Country</th>
                            <th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentViewersResult->num_rows > 0): ?>
                            <?php while ($row = $recentViewersResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['country']); ?></td>
                                    <td><?php echo $row['created_at']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No recent registrations found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Viewing Sessions -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Recent Viewing Sessions</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th>Viewer Name</th>
                            <th>Email</th>
                            <th>People Watching</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($sessionsResult->num_rows > 0): ?>
                            <?php while ($row = $sessionsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['number_of_viewers']); ?></td>
                                    <td><?php echo $row['viewed_at']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No viewing sessions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
