
<?php include "../backend/manage_checkout.php"?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Szállítási adatok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
 

    <div class="container mt-5">
        <h1 class="text-center text-gold">Szállítási adatok</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($hasShippingInfo): ?>
            <div class="alert alert-info" id="existingAddressAlert">
                Úgy tűnik, korábban megadott szállítási címet. Szeretnéd használni?
                <form method="POST" action="">
                    <input type="hidden" name="use_existing" value="1">
                    <button type="submit" class="btn btn-sm btn-outline-success">Igen, használom a meglévőt</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('newAddressForm').style.display = 'block'; document.getElementById('existingAddressAlert').style.display = 'none';">Nem, új címet adok meg</button>
                </form>
            </div>
            <form action="" method="POST" id="newAddressForm" style="display: none;">
                <input type="hidden" name="new_address" value="1">
                <h2 class="mt-4 text-center text-gold">Új szállítási cím megadása</h2>
                <div class="mb-3">
                    <label for="name" class="form-label">Név</label>
                    <input type="text" class="form-control" name="name" value="<?= $name ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Cím</label>
                    <input type="text" class="form-control" name="address" value="<?= $address ?>" required>
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">Város</label>
                    <input type="text" class="form-control" name="city" value="<?= $city ?>" required>
                </div>
                <div class="mb-3">
                    <label for="zip" class="form-label">Irányítószám</label>
                    <input type="text" class="form-control" name="zip" value="<?= $zip ?>" required>
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Ország</label>
                    <input type="text" class="form-control" name="country" value="<?= $country ?>" required>
                </div>
                <button type="submit" class="btn btn-gold w-100">Tovább a fizetéshez</button>
            </form>
        <?php else: ?>
            <form action="" method="POST">
                <input type="hidden" name="new_address" value="1">
                <div class="mb-3">
                    <label for="name" class="form-label">Név</label>
                    <input type="text" class="form-control" name="name" value="<?= $name ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Cím</label>
                    <input type="text" class="form-control" name="address" value="<?= $address ?>" required>
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">Város</label>
                    <input type="text" class="form-control" name="city" value="<?= $city ?>" required>
                </div>
                <div class="mb-3">
                    <label for="zip" class="form-label">Irányítószám</label>
                    <input type="text" class="form-control" name="zip" value="<?= $zip ?>" required>
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Ország</label>
                    <input type="text" class="form-control" name="country" value="<?= $country ?>" required>
                </div>
                
                <button type="submit" class="btn btn-gold w-100">Tovább a fizetéshez</button>
                <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="save_address" value="1" id="saveAddress">
    <label class="form-check-label" for="saveAddress">
        Cím mentése a profilomba
    </label>
</div>

            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>