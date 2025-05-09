<?php
/**
 * Form Wrapper
 *
 * @since 1.0.0
 * @package Global_Variables
 */
?>

<div class="gv_form_wrapper">
    <form class="gv_custom_action_handler" method="post">
        <input type="text" name="name" placeholder="Identifier">
        <input type="text" name="custom_input" placeholder="Value"/>
        <input type="text" hidden name="action" value="add_new_variable">
        <button type="submit">Create New Variable</button>
    </form>
</div>