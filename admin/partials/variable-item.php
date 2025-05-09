<?php
/**
 * Single Variable Item Partial
 * Displays individual saved variables with update & delete forms.
 *
 * @since 1.0.0
 * @package Global_Variables
 */

 // Ensure `$variable` is passed to this file
if (!isset($variable)) {
    return;
}

// Clean input for security
$cleanedText = stripslashes($variable->text);
?>

<div class="variable_item">
    <form class="gv_custom_action_handler" method="post">
        <input type="text" name="name" value="<?php echo esc_attr($variable->name); ?>" placeholder="Identifier">
        <input type="text" name="custom_input" value="<?php echo $cleanedText; ?>" placeholder="Value">
        <input type="hidden" name="id" value="<?php echo esc_attr($variable->id); ?>">
        <input type="hidden" name="action" value="update_variable">
        <button type="submit">Update</button>
    </form>

    <form class="gv_custom_action_handler" method="post">
        <input type="hidden" name="id" value="<?php echo esc_attr($variable->id); ?>">
        <input type="hidden" name="action" value="delete_variable">
        <button type="submit">Delete</button>
    </form>
</div>