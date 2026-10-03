<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solis Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">S</div>
                <div>
                    <strong>Solis</strong>
                    <span>Admin</span>
                </div>
            </div>

            <nav class="nav">
                <a class="nav-item active" href="/">
                    <span>Dashboard</span>
                </a>
                <a class="nav-item" href="#">
                    <span>Users</span>
                </a>
                <a class="nav-item" href="#">
                    <span>Devices</span>
                </a>
                <a class="nav-item" href="#">
                    <span>Permissions</span>
                </a>
                <a class="nav-item" href="#">
                    <span>Logs</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="admin-card">
                    <span class="status-dot"></span>
                    <div>
                        <strong>Admin</strong>
                        <span>System control</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="content">
            <header class="topbar">
                <div>
                    <p class="eyebrow">SOLIS CONTROL PANEL</p>
                    <h1>Dashboard</h1>
                </div>
                <div class="topbar-meta">
                    <span class="connection"><span class="status-dot"></span>System online</span>
                </div>
            </header>

            <section class="stats">
                <article class="stat-card">
                    <span class="stat-label">Total users</span>
                    <strong>—</strong>
                    <span class="stat-muted">Not connected</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Active users</span>
                    <strong>—</strong>
                    <span class="stat-muted">Not connected</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Disabled</span>
                    <strong>—</strong>
                    <span class="stat-muted">Not connected</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Devices</span>
                    <strong>—</strong>
                    <span class="stat-muted">Not connected</span>
                </article>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">ACCOUNT MANAGEMENT</p>
                        <h2>Users</h2>
                    </div>
                    <button type="button" class="button" disabled>Add user</button>
                </div>

                <div class="empty-state">
                    <div class="empty-icon">S</div>
                    <h3>User management is not connected yet</h3>
                    <p>The interface is ready. Supabase integration and account actions will be added next.</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
