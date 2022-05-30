<?php
/**
 * @var $post_id string
 * @var $post WPUNISENDER_Form
 */

// don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

function wpunisender_admin_save_button($post_id)
{
    static $button = '';

    if (!empty($button)) {
        echo $button;
        return;
    }

    $nonce = wp_create_nonce('wpunisender-save-form_' . $post_id);

    $onclick = sprintf(
        "this.form._wpnonce.value = '%s';"
        . " this.form.action.value = 'save';"
        . " return true;",
        $nonce);

    $button = sprintf(
        '<input type="submit" class="button-primary" name="wpunisender-save" value="%1$s" onclick="%2$s" />',
        esc_attr(__('Сохранить', 'unisender')),
        $onclick);

    echo $button;
}

?>
    <div class="wrap" id="wpunisender-form-editor">

        <h1 class="wp-heading-inline"><?php
            if ($post->initial()) {
                echo esc_html(__('Добавить новую форму', 'unisender'));
            } else {
                echo esc_html(__('Редактировать форму', 'unisender'));
            }
            ?></h1>

        <?php
        if (!$post->initial()
            and current_user_can('wpunisender_edit_forms')) {
            echo wpunisender_link(
                menu_page_url('wpunisender-new', false),
                __('Добавить новую', 'unisender'),
                array('class' => 'page-title-action')
            );
        }
        ?>

        <hr class="wp-header-end">

        <?php
        do_action('wpunisender_admin_warnings',
            $post->initial() ? 'wpunisender-new' : 'wpunisender',
            wpunisender_current_action(),
            $post
        );

        do_action('wpunisender_admin_notices',
            $post->initial() ? 'wpunisender-new' : 'wpunisender',
            wpunisender_current_action(),
            $post
        );
        ?>

        <?php
        if ($post) :

            if (current_user_can('wpunisender_edit_form', $post_id)) {
                $disabled = '';
            } else {
                $disabled = ' disabled="disabled"';
            }
            ?>

            <form method="post" novalidate
                  action="<?php echo esc_url(add_query_arg(array('post' => $post_id),
                      menu_page_url('wpunisender', false))); ?>"
                  id="wpunisender-admin-form-element"<?php do_action('wpunisender_post_edit_form_tag'); ?>>
                <?php
                if (current_user_can('wpunisender_edit_form', $post_id)) {
                    wp_nonce_field('wpunisender-save-form_' . $post_id);
                }
                ?>
                <input type="hidden" id="post_ID" name="post_ID" value="<?php echo (int)$post_id; ?>"/>
                <input type="hidden" id="wpunisender-locale" name="wpunisender-locale"
                       value="<?php echo esc_attr($post->locale()); ?>"/>
                <input type="hidden" id="hiddenaction" name="action" value="save"/>

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <div id="titlediv">
                                <div id="titlewrap">
                                    <label class="screen-reader-text" id="title-prompt-text"
                                           for="title"><?php echo esc_html(__('Введите название здесь',
                                            'unisender')); ?></label>
                                    <?php
                                    $posttitle_atts = array(
                                        'type' => 'text',
                                        'name' => 'post_title',
                                        'size' => 30,
                                        'value' => $post->initial() ? '' : $post->title(),
                                        'id' => 'title',
                                        'spellcheck' => 'true',
                                        'autocomplete' => 'off',
                                        'disabled' =>
                                            current_user_can('wpunisender_edit_form', $post_id) ? '' : 'disabled',
                                    );

                                    echo sprintf('<input %s />', wpunisender_format_atts($posttitle_atts));
                                    ?>
                                </div><!-- #titlewrap -->

                                <div class="inside">
                                    <?php
                                    if (!$post->initial()) :
                                        ?>
                                        <p class="description">
                                            <label for="wpunisender-shortcode"><?php echo esc_html(__("Скопируйте этот шорткод и вставьте его в содержимое своей записи, страницы или текстового виджета:",
                                                    'unisender')); ?></label>
                                            <span class="shortcode wp-ui-highlight"><input type="text"
                                                                                           id="wpunisender-shortcode"
                                                                                           onfocus="this.select();"
                                                                                           readonly="readonly"
                                                                                           class="large-text code"
                                                                                           value="<?php echo esc_attr($post->shortcode()); ?>"/></span>
                                        </p>
                                    <?php
                                    endif;
                                    ?>
                                    <div id="metadiv">
                                        <table class="form-table">
                                            <tbody>
                                            <tr>
                                                <th scope="row"><?php echo esc_html(__('Привязать к списку Unisender:', 'unisender')) ?></th>
                                                <td>
                                                    <?php
                                                    $contact_lists = $post->get_contact_lists();
                                                    $contact_list_selected = esc_attr($post->prop('contact_list')); ?>
                                                    <select name="wpunisender-contact-list"
                                                            id="wpunisender-contact-list">
                                                        <option value=""></option>
                                                        <?php foreach ($contact_lists as $contact_list) : ?>
                                                            <option <?php if ($contact_list['id'] == $contact_list_selected) {
                                                                echo 'selected="selected"';
                                                            } ?> value="<?= $contact_list['id'] ?>"><?= $contact_list['title'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div><!-- #titlediv -->
                        </div><!-- #post-body-content -->

                        <div id="postbox-container-1" class="postbox-container">
                            <?php if (current_user_can('wpunisender_edit_form', $post_id)) : ?>
                                <div id="submitdiv" class="postbox">
                                    <h3><?php echo esc_html(__('Статус', 'unisender')); ?></h3>
                                    <div class="inside">
                                        <div class="submitbox" id="submitpost">

                                            <div id="minor-publishing-actions">

                                                <div class="hidden">
                                                    <input type="submit" class="button-primary" name="wpunisender-save"
                                                           value="<?php echo esc_attr(__('Сохранить', 'unisender')); ?>"/>
                                                </div>

                                                <?php
                                                if (!$post->initial()) :
                                                    $copy_nonce = wp_create_nonce('wpunisender-copy-form_' . $post_id);
                                                    ?>
                                                    <input type="submit" name="wpunisender-copy" class="copy button"
                                                           value="<?php echo esc_attr(__('Дублировать',
                                                               'unisender')); ?>" <?php echo "onclick=\"this.form._wpnonce.value = '$copy_nonce'; this.form.action.value = 'copy'; return true;\""; ?> />
                                                <?php endif; ?>
                                            </div><!-- #minor-publishing-actions -->

                                            <div id="misc-publishing-actions">
                                                <?php do_action('wpunisender_admin_misc_pub_section', $post_id); ?>
                                            </div><!-- #misc-publishing-actions -->

                                            <div id="major-publishing-actions">

                                                <?php
                                                if (!$post->initial()) :
                                                    $delete_nonce = wp_create_nonce('wpunisender-delete-form_' . $post_id);
                                                    ?>
                                                    <div id="delete-action">
                                                        <input type="submit" name="wpunisender-delete"
                                                               class="delete submitdelete"
                                                               value="<?php echo esc_attr(__('Удалить',
                                                                   'unisender')); ?>" <?php echo "onclick=\"if (confirm('" . esc_js(__("Вы уверены, что хотите удалить эту форму?",
                                                                'unisender')) . "')) {this.form._wpnonce.value = '$delete_nonce'; this.form.action.value = 'delete'; return true;} return false;\""; ?> />
                                                    </div><!-- #delete-action -->
                                                <?php endif; ?>

                                                <div id="publishing-action">
                                                    <span class="spinner"></span>
                                                    <?php wpunisender_admin_save_button($post_id); ?>
                                                </div>
                                                <div class="clear"></div>
                                            </div><!-- #major-publishing-actions -->
                                        </div><!-- #submitpost -->
                                    </div>
                                </div><!-- #submitdiv -->
                            <?php endif; ?>

                        </div><!-- #postbox-container-1 -->

                        <div id="postbox-container-2" class="postbox-container">

                            <div id="form-editor">
<!--                        <textarea id="wpunisender-form" name="wpunisender-form" cols="100" rows="24"-->
<!--                                  class="large-text code">--><?php //echo esc_textarea($post->prop('form')); ?><!--</textarea>-->
                                <?php
                                $api_key = get_option('wpunisender_api_key');
                                $api = new \Unisender\ApiWrapper\UnisenderApi($api_key);
                                $fields = json_decode($api->getFields());
                                ?>
                                <script>
                                    const UNISENDER_ADDITIONAL_FIELDS = '<?= json_encode($fields->result) ?>';
                                </script>
                                <input type="hidden" id="wpunisender-form" name="wpunisender-form" data-unisender-hidden-field value="<?php echo esc_textarea($post->prop('form')); ?>">
                                <div id="unisender-form-builder"></div>
                            </div><!-- #form-editor -->

                            <?php if (current_user_can('wpunisender_edit_form', $post_id)) : ?>
                                <p class="submit"><?php wpunisender_admin_save_button($post_id); ?></p>
                            <?php endif; ?>

                        </div><!-- #postbox-container-2 -->

                    </div><!-- #post-body -->
                    <br class="clear"/>
                </div><!-- #poststuff -->
            </form>

        <?php endif; ?>

    </div><!-- .wrap -->

<?php

do_action('wpunisender_admin_footer', $post);
