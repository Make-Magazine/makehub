<table class="notes-listing">
    <thead>
        <tr>
            <th><?php echo esc_html( get_the_title($group_id) ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $users as $user ): ?>
            <tr>
                <td><a href="<?php echo esc_url( get_the_permalink() . '?user=' . $user->ID ); ?>"><?php echo esc_html($user->display_name); ?></a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
