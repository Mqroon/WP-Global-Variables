<?php
/**
 * Variables Wrapper
 *
 * @since 1.0.0
 * @package Global_Variables
 */

// Ensure `$results` is passed to this file
if (!isset($results) || empty($results)) {
    echo '
    <div class="variable_item">
        No variables found
    </div>';
    return;
}
?>

<div id="gv_variables_wrapper">
    <h2 class="gv_variables_h2">Saved Variables
        <span id="loading_icon" class="dashicons dashicons-update" style="display: none;"></span> <!-- Spinning icon -->
        <span id="save_icon" class="dashicons dashicons-saved" style="display: block;"></span> <!-- Checkmark icon -->
    </h2>
    <div class="gv_variables_header">
        <p>Identifier</p>
        <p>Value</p>
    </div>

    <?php foreach ($results as $variable) {
        include plugin_dir_path(__FILE__) . 'variable-item.php';
    } ?>
</div>