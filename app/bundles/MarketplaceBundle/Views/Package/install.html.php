<?php

declare(strict_types=1);

use Mautic\MarketplaceBundle\DTO\PackageDetail;

/** @var PackageDetail $packageDetail */
$packageDetail = $packageDetail;

try {
    $latestVersion = $packageDetail->versions->findLatestVersionPackage();
} catch (\Throwable $e) {
    $latestVersionException = $e;
}
?>
<div class="text-center" id="marketplace-installation-in-progress">
    <p><strong><?php echo $packageDetail->packageBase->getHumanPackageName(); ?></strong> is being installed. This might take a while...</p>
    <div class="spinner">
        <i class="fa fa-spin fa-spinner"></i>
    </div>
</div>
<div style="display: none" class="text-center" id="marketplace-installation-failed">
    <p>Something went wrong while installing <strong><?php echo $packageDetail->packageBase->getHumanPackageName(); ?></strong>. This is the error:</p>
    <textarea class="form-control" readonly id="marketplace-installation-failed-details"></textarea>
</div>
<div style="display: none" class="text-center" id="marketplace-installation-success">
    <p>Successfully installed <strong><?php echo $packageDetail->packageBase->getHumanPackageName(); ?></strong>!</p>
    <p><a class="btn btn-primary" href="<?php echo $view['router']->path('mautic_plugin_index'); ?>">Go to the plugin page to activate the plugin</a></p>
</div>

<script>
    const installPackageResetView = () => {
        mQuery('#marketplace-installation-in-progress').show();
        mQuery('#marketplace-installation-success').hide();
        mQuery('#marketplace-installation-failed').hide();
    }

    installPackageResetView();

    Mautic.Marketplace.installPackage(
        '<?php echo htmlspecialchars($packageDetail->packageBase->getVendorName(), ENT_QUOTES); ?>',
        '<?php echo htmlspecialchars($packageDetail->packageBase->getPackageName(), ENT_QUOTES); ?>',
        (response) => {
            if (response.success) {
                mQuery('#marketplace-installation-in-progress').hide();
                mQuery('#marketplace-installation-success').show();
            } else if (response.redirect) {
                window.location = response.redirect;
            }
        },
        (request, textStatus, errorThrown) => {
            let error;

            try {
                error = (JSON.parse(request.responseText)).error;
            } catch (e) {
                error = 'An unknown error occurred. Please check the logs for more details.';
                console.error(request.responseText);
                console.error(e);
            }

            mQuery('#marketplace-installation-in-progress').hide();
            mQuery('#marketplace-installation-failed').show();
            mQuery('#marketplace-installation-failed-details').text(error);
        }
    );
</script>