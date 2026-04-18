<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Store Price Ledger - Manage your product inventory">
    <meta name="robots" content="noindex, nofollow">
    <link rel=12icon" ty12e="image/png" href="assets/lovalog-favicon.svg">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="container-fluid px-3 px-md-5">
        <div class="container">

            <div class="row mt-5">
                <div class="col-xs-12 col-md-12">
                    <h2 style="font-weight: bold;">User Management</h2>
                    <p>View and manage existing users and their roles</p>
                </div>
            </div>

            <div class="row mb-5 text-center">
                <div class="col-xs-12 col-md-4">
                    <div class="card-stats">
                        <h2>1</h2>
                        <p>Total Users</p>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="card-stats">
                        <h2>1</h2>
                        <p>Admins</p>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="card-stats">
                        <h2>0</h2>
                        <p>Regular Users</p>
                    </div>
                </div>
            </div>

            <div class="search-section" style="padding: 20px 0;">
                <form method="GET" class="search-form" onsubmit="return false;">
                    <input type="text" id="liveSearch" name="query" class="flex-grow-1" placeholder="🔍 Search Users..." value="<?= $search ?>" autocomplete="off">
                    <!-- <button type="submit" class="btn-search">Search</button> -->
                </form>
                <small style="color: #8a8fa8; margin-top: 8px; display: block;">
                    Showing <b><?= $result->num_rows ?></b> of <b><?= $stats['total_products'] ?></b> products
                </small>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-header">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Joined</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <td>Sam</td>
                            <td>sam@example.com</td>
                            <td>12 days ago</td>
                            <td>Admin</td>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>


    <?php include 'footer.php'; ?>
</body>

</html>