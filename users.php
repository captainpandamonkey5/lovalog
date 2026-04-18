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
                        <h2>0</h2>
                        <p>Total Users</p>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="card-stats">
                        <h2>0</h2>
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

        </div>
    </main>


    <?php include 'footer.php'; ?>
</body>

</html>