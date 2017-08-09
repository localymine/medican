<?php
/*
  Field Name: Profile working hours
 */
?>
<?php
global $post;
$days = array(
    '1' => esc_html__('Monday', 'medican'),
    '2' => esc_html__('Tuesday', 'medican'),
    '3' => esc_html__('Wednesday', 'medican'),
    '4' => esc_html__('Thursday', 'medican'),
    '5' => esc_html__('Friday', 'medican'),
    '6' => esc_html__('Saturday', 'medican'),
    '7' => esc_html__('Sunday', 'medican'),
);

$working_hours = get_post_meta($post->ID, 'working-hours', true);

$options = get_option(AZEXO_FRAMEWORK);

$not_empty = array_filter((array) $working_hours);

if (!empty($not_empty)):
    ?>
    <table class="working-hours">
        <?php print (isset($options['profile-working-hours_prefix']) && !empty($options['profile-working-hours_prefix'])) ? '<caption>' . esc_html($options['profile-working-hours_prefix']) . '</caption>' : ''; ?>
        <thead>
            <tr>
                <th><?php esc_attr_e('Day', 'medican'); ?></th>
                <th><?php esc_attr_e('Open', 'medican'); ?></th>
                <th><?php esc_attr_e('Close', 'medican'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($days as $day => $day_name) {
                ?>
                <tr>
                    <td><label><?php print $day_name; ?></label></td>
                    <?php
                    if (empty($working_hours['open-' . $day]) || empty($working_hours['close-' . $day])) {
                        ?>
                        <td class="closed" colspan="2"><?php esc_attr_e('Closed', 'medican'); ?></td>
                        <?php
                    } else {
                        ?>
                        <td class="open">
                            <?php
                            print date("g:i a", strtotime(esc_html($working_hours['open-' . $day])));
                            ?>
                        </td>
                        <td class="close">
                            <?php
                            print date("g:i a", strtotime(esc_html($working_hours['close-' . $day])));
                            ?>
                        </td>
                        <?php
                    }
                    ?>

                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php




endif;