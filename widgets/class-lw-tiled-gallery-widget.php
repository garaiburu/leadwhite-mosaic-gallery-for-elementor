<?php
/**
 * LW Tiled Gallery — Elementor Widget
 *
 * Provides a tiled mosaic gallery widget for Elementor. Images are grouped
 * into rows automatically based on their aspect ratios using a shape library
 * adapted from Jetpack's open-source tiled gallery grouper. Row heights and
 * column widths are calculated so all images in a row share the same height.
 *
 * Layout approach (following Jetpack):
 * PHP calculates exact pixel dimensions at a nominal container width (1000px)
 * and stores them as data-original-width / data-original-height attributes.
 * JS reads the actual container width and scales all dimensions proportionally.
 * This avoids percentage rounding errors and container padding issues entirely.
 *
 * @package LW_Tiled_Gallery
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class LW_Tiled_Gallery_Widget
 */
class LW_Tiled_Gallery_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'lw_tiled_gallery';
    }

    public function get_title() {
        return 'Tiled Gallery';
    }

    public function get_icon() {
        return 'eicon-gallery-masonry';
    }

    public function get_categories() {
        return array( 'general' );
    }

    public function get_style_depends() {
        return array( 'lw-tiled-gallery' );
    }

    public function get_script_depends() {
        return array( 'lw-tiled-gallery' );
    }

    // =========================================================================
    // Controls
    // =========================================================================

    protected function register_controls() {

        $this->start_controls_section( 'section_images', array(
            'label' => 'Images',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'gallery', array(
            'label'   => 'Gallery Images',
            'type'    => \Elementor\Controls_Manager::GALLERY,
            'default' => array(),
        ) );

        $this->end_controls_section();

        $this->start_controls_section( 'section_overlay_content', array(
            'label' => 'Overlay',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'overlay_enabled', array(
            'label'        => 'Background',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
            'default'      => '',
        ) );

        $this->add_control( 'overlay_title', array(
            'label'   => 'Title',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => array(
                ''            => 'None',
                'title'       => 'Title',
                'caption'     => 'Caption',
                'alt'         => 'Alt',
                'description' => 'Description',
            ),
            'default' => '',
        ) );

        $this->add_control( 'overlay_description', array(
            'label'   => 'Description',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => array(
                ''            => 'None',
                'title'       => 'Title',
                'caption'     => 'Caption',
                'alt'         => 'Alt',
                'description' => 'Description',
            ),
            'default' => '',
        ) );

        $this->end_controls_section();

        $this->start_controls_section( 'section_layout', array(
            'label' => 'Layout',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'gap', array(
            'label'      => 'Gap (px)',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array(
                'px' => array( 'min' => 0, 'max' => 20, 'step' => 1 ),
            ),
            'default'    => array( 'size' => 4, 'unit' => 'px' ),
        ) );

        $this->add_control( 'mobile_breakpoint', array(
            'label'      => 'Stack below (px)',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array(
                'px' => array( 'min' => 320, 'max' => 768, 'step' => 10 ),
            ),
            'default'    => array( 'size' => 480, 'unit' => 'px' ),
        ) );

        $this->end_controls_section();

        $this->start_controls_section( 'section_interaction', array(
            'label' => 'Interaction',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'link_to', array(
            'label'   => 'Link images to',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => array(
                'none'            => 'None',
                'lightbox'        => 'Lightbox',
                'attachment_page' => 'Attachment page',
                'media_file'      => 'Media file',
            ),
            'default' => 'lightbox',
        ) );

        $this->end_controls_section();

        // Style: Image
        $this->start_controls_section( 'section_style_image', array(
            'label' => 'Image',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->start_controls_tabs( 'image_border_tabs' );

        $this->start_controls_tab( 'image_border_normal', array( 'label' => 'Normal' ) );

        $this->add_control( 'image_border_color', array(
            'label'     => 'Border Colour',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .lw-tiled-item img' => 'border-color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control(
            \Elementor\Group_Control_Css_Filter::get_type(),
            array(
                'name'     => 'image_css_filters',
                'selector' => '{{WRAPPER}} .lw-tiled-item img',
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab( 'image_border_hover', array( 'label' => 'Hover' ) );

        $this->add_control( 'image_border_color_hover', array(
            'label'     => 'Border Colour',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .lw-tiled-item:hover img' => 'border-color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control(
            \Elementor\Group_Control_Css_Filter::get_type(),
            array(
                'name'     => 'image_css_filters_hover',
                'selector' => '{{WRAPPER}} .lw-tiled-item:hover img',
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control( 'image_border_width', array(
            'label'      => 'Border Width',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array(
                'px' => array( 'min' => 0, 'max' => 10, 'step' => 1 ),
            ),
            'selectors'  => array(
                '{{WRAPPER}} .lw-tiled-item img' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
            ),
        ) );

        $this->add_control( 'image_border_radius', array(
            'label'      => 'Border Radius',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array(
                'px' => array( 'min' => 0, 'max' => 50, 'step' => 1 ),
            ),
            'selectors'  => array(
                '{{WRAPPER}} .lw-tiled-item'     => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
                '{{WRAPPER}} .lw-tiled-item img' => 'border-radius: {{SIZE}}{{UNIT}};',
            ),
        ) );

        $this->add_control( 'image_hover_animation', array(
            'label'   => 'Hover Animation',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => array(
                'none'    => 'None',
                'zoom_in' => 'Zoom In',
            ),
            'default' => 'none',
        ) );

        $this->add_control( 'image_animation_duration', array(
            'label'     => 'Animation Duration (ms)',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => array(
                'px' => array( 'min' => 100, 'max' => 2000, 'step' => 100 ),
            ),
            'default'   => array( 'size' => 400 ),
            'condition' => array( 'image_hover_animation!' => 'none' ),
        ) );

        $this->end_controls_section();

        // Style: Overlay
        $this->start_controls_section( 'section_style_overlay', array(
            'label' => 'Overlay',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'overlay_color', array(
            'label'     => 'Overlay Colour',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => 'rgba(0,0,0,0.5)',
            'selectors' => array(
                '{{WRAPPER}} .lw-tiled-item .lw-overlay' => 'background-color: {{VALUE}};',
            ),
        ) );

        $this->add_control( 'overlay_animation', array(
            'label'   => 'Hover Animation',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => array(
                'fade'     => 'Fade In',
                'slide_up' => 'Slide Up',
                'none'     => 'None',
            ),
            'default' => 'fade',
        ) );

        $this->add_control( 'overlay_animation_duration', array(
            'label'     => 'Animation Duration (ms)',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => array(
                'px' => array( 'min' => 100, 'max' => 2000, 'step' => 100 ),
            ),
            'default'   => array( 'size' => 300 ),
            'condition' => array( 'overlay_animation!' => 'none' ),
        ) );

        $this->end_controls_section();

        // Style: Content
        $this->start_controls_section( 'section_style_content', array(
            'label' => 'Content',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'text_alignment', array(
            'label'     => 'Alignment',
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => array(
                'left'   => array( 'title' => 'Left',   'icon' => 'eicon-text-align-left' ),
                'center' => array( 'title' => 'Centre', 'icon' => 'eicon-text-align-center' ),
                'right'  => array( 'title' => 'Right',  'icon' => 'eicon-text-align-right' ),
            ),
            'default'   => 'center',
            'selectors' => array(
                '{{WRAPPER}} .lw-overlay-content' => 'text-align: {{VALUE}};',
            ),
        ) );

        $this->add_control( 'vertical_position', array(
            'label'   => 'Vertical Position',
            'type'    => \Elementor\Controls_Manager::CHOOSE,
            'options' => array(
                'top'    => array( 'title' => 'Top',    'icon' => 'eicon-v-align-top' ),
                'middle' => array( 'title' => 'Middle', 'icon' => 'eicon-v-align-middle' ),
                'bottom' => array( 'title' => 'Bottom', 'icon' => 'eicon-v-align-bottom' ),
            ),
            'default' => 'middle',
        ) );

        $this->add_control( 'content_padding', array(
            'label'      => 'Padding (px)',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px' ),
            'range'      => array(
                'px' => array( 'min' => 0, 'max' => 60, 'step' => 2 ),
            ),
            'default'    => array( 'size' => 20, 'unit' => 'px' ),
            'selectors'  => array(
                '{{WRAPPER}} .lw-overlay-content' => 'padding: {{SIZE}}{{UNIT}};',
            ),
        ) );

        $this->add_control( 'title_heading', array(
            'label'     => 'Title',
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'title_color', array(
            'label'     => 'Colour',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => array(
                '{{WRAPPER}} .lw-overlay-title' => 'color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .lw-overlay-title',
            )
        );

        $this->add_control( 'description_heading', array(
            'label'     => 'Description',
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'description_color', array(
            'label'     => 'Colour',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => array(
                '{{WRAPPER}} .lw-overlay-description' => 'color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'description_typography',
                'selector' => '{{WRAPPER}} .lw-overlay-description',
            )
        );

        $this->end_controls_section();
    }

    // =========================================================================
    // Grouper
    // =========================================================================

    private $shapes_used  = array();
    private $current_shape = '';

    private function image_ratio( $image ) {
        if ( $image['height'] > 0 ) {
            return $image['width'] / $image['height'];
        }
        return 1;
    }

    private function sum_ratios( $images, $count ) {
        $sum = 0;
        for ( $i = 0; $i < $count && $i < count( $images ); $i++ ) {
            $sum += $this->image_ratio( $images[ $i ] );
        }
        return $sum;
    }

    private function is_landscape( $image ) {
        $r = $this->image_ratio( $image );
        return $r >= 1 && $r < 2;
    }

    private function is_portrait( $image ) {
        return $this->image_ratio( $image ) < 1;
    }

    private function is_panoramic( $image ) {
        return $this->image_ratio( $image ) >= 2;
    }

    private function is_not_as_previous( $n = 1 ) {
        if ( empty( $this->shapes_used ) ) {
            return true;
        }
        $recent = array_slice( $this->shapes_used, -$n );
        return ! in_array( $this->current_shape, $recent, true );
    }

    private function try_shape( $shape_name, $images, $images_left ) {
        $this->current_shape = $shape_name;

        switch ( $shape_name ) {
            case 'Panoramic':
                if ( $images_left >= 1 && $this->is_panoramic( $images[0] ) ) {
                    return array( 1 );
                }
                break;
            case 'Symmetric_Row':
                if ( $this->is_not_as_previous( 5 ) && $images_left > 4 && $images_left !== 5 &&
                    $this->is_portrait( $images[0] ) && $this->is_landscape( $images[1] ) &&
                    $this->is_landscape( $images[2] ) && $this->is_portrait( $images[3] ) ) {
                    return array( 1, 2, 1 );
                }
                break;
            case 'One_Three':
                if ( $this->is_not_as_previous( 3 ) && $images_left > 4 &&
                    $this->is_portrait( $images[0] ) && $this->is_landscape( $images[1] ) &&
                    $this->is_landscape( $images[2] ) && $this->is_landscape( $images[3] ) ) {
                    return array( 1, 3 );
                }
                break;
            case 'Three_One':
                if ( $this->is_not_as_previous( 3 ) && $images_left > 4 &&
                    $this->is_portrait( $images[3] ) && $this->is_landscape( $images[0] ) &&
                    $this->is_landscape( $images[1] ) && $this->is_landscape( $images[2] ) ) {
                    return array( 3, 1 );
                }
                break;
            case 'One_Two':
                if ( $this->is_not_as_previous( 3 ) && $images_left >= 3 &&
                    $this->image_ratio( $images[0] ) < 1.6 &&
                    $this->image_ratio( $images[1] ) >= 0.9 && $this->image_ratio( $images[1] ) < 2.0 &&
                    $this->image_ratio( $images[2] ) >= 0.9 && $this->image_ratio( $images[2] ) < 2.0 ) {
                    return array( 1, 2 );
                }
                break;
            case 'Two_One':
                if ( $this->is_not_as_previous( 3 ) && $images_left >= 3 &&
                    $this->image_ratio( $images[2] ) < 1.6 &&
                    $this->image_ratio( $images[0] ) >= 0.9 && $this->image_ratio( $images[0] ) < 2.0 &&
                    $this->image_ratio( $images[1] ) >= 0.9 && $this->image_ratio( $images[1] ) < 2.0 ) {
                    return array( 2, 1 );
                }
                break;
            case 'Four':
                if ( $this->is_not_as_previous() && $images_left >= 4 &&
                    ( ( $this->sum_ratios( $images, 4 ) < 3.5 && $images_left > 5 ) ||
                      ( $this->sum_ratios( $images, 4 ) < 7   && $images_left === 4 ) ) ) {
                    return array( 1, 1, 1, 1 );
                }
                break;
            case 'Three':
                if ( $images_left >= 3 && ! in_array( $images_left, array( 4, 6 ), true ) &&
                    $this->is_not_as_previous( 3 ) ) {
                    $ratio     = $this->sum_ratios( $images, 3 );
                    $symmetric = $images_left > 2 && $this->image_ratio( $images[0] ) === $this->image_ratio( $images[2] );
                    if ( $ratio < 2.5 || ( $ratio < 5 && $symmetric ) ) {
                        return array( 1, 1, 1 );
                    }
                }
                break;
        }
        return false;
    }

    private $shape_priority = array(
        'Symmetric_Row', 'One_Three', 'Three_One', 'One_Two',
        'Four', 'Three', 'Two_One', 'Panoramic',
    );

    private function apply_shape( $shape, &$images ) {
        $groups = array();
        foreach ( $shape as $group_size ) {
            $group = array();
            for ( $i = 0; $i < $group_size; $i++ ) {
                $group[] = array_shift( $images );
            }
            $groups[] = $group;
        }
        return $groups;
    }

    private function get_row_groups( &$images ) {
        $images_left = count( $images );
        foreach ( $this->shape_priority as $shape_name ) {
            $shape = $this->try_shape( $shape_name, $images, $images_left );
            if ( $shape !== false && $images_left >= array_sum( $shape ) ) {
                $this->shapes_used[] = $shape_name;
                return $this->apply_shape( $shape, $images );
            }
        }
        if ( $images_left >= 2 ) {
            $this->shapes_used[] = 'Two';
            return array( array( array_shift( $images ) ), array( array_shift( $images ) ) );
        }
        $this->shapes_used[] = 'One';
        return array( array( array_shift( $images ) ) );
    }

    private function group_images( $images ) {
        $this->shapes_used = array();
        $rows              = array();
        $remaining         = $images;
        while ( ! empty( $remaining ) ) {
            $rows[] = $this->get_row_groups( $remaining );
        }
        return $rows;
    }

    // =========================================================================
    // Pixel dimension calculation (Jetpack approach)
    //
    // Calculates exact pixel widths and heights at a nominal container width.
    // JS scales these proportionally — no percentage rounding issues.
    // =========================================================================

    private function group_ratio( $group ) {
        $inverse_sum = 0;
        foreach ( $group as $image ) {
            $ratio = $this->image_ratio( $image );
            if ( $ratio ) {
                $inverse_sum += 1 / $ratio;
            }
        }
        return $inverse_sum > 0 ? 1 / $inverse_sum : 1;
    }

    /**
     * Calculate pixel widths and row height for a row at the nominal width.
     *
     * @param array $groups
     * @param int   $container_width Nominal width in px
     * @param int   $gap             Gap in px
     * @return array { row_height, group_widths[] }
     */
    private function calculate_row_dimensions( $groups, $container_width, $gap ) {
        $row_ratio = 0;
        foreach ( $groups as $group ) {
            $row_ratio += $this->group_ratio( $group );
        }
        if ( $row_ratio <= 0 ) { $row_ratio = 1; }

        $weighted_ratio = 0;
        foreach ( $groups as $group ) {
            $weighted_ratio += $this->group_ratio( $group ) * count( $group );
        }
        if ( $weighted_ratio <= 0 ) { $weighted_ratio = 1; }

        $row_height = ( 1 / $row_ratio ) * ( $container_width - $gap * ( count( $groups ) - $weighted_ratio ) );

        $group_widths = array();
        $total_width  = 0;
        foreach ( $groups as $i => $group ) {
            if ( $i === count( $groups ) - 1 ) {
                // Last group gets the remainder — eliminates rounding gaps
                $group_widths[] = (int) round( $container_width - $total_width - $gap * $i );
            } else {
                $w              = (int) round( ( $row_height - $gap * count( $group ) ) * $this->group_ratio( $group ) + $gap );
                $group_widths[] = $w;
                $total_width   += $w;
            }
        }

        return array(
            'row_height'   => (int) round( $row_height ),
            'group_widths' => $group_widths,
        );
    }

    // =========================================================================
    // Overlay text helper
    // =========================================================================

    private function get_overlay_text( $field, $attachment_id ) {
        if ( ! $field ) { return ''; }
        switch ( $field ) {
            case 'title':       return get_the_title( $attachment_id );
            case 'caption':     return wp_get_attachment_caption( $attachment_id );
            case 'alt':         return get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
            case 'description':
                $post = get_post( $attachment_id );
                return $post ? $post->post_content : '';
        }
        return '';
    }

    // =========================================================================
    // Render
    // =========================================================================

    protected function render() {
        $settings = $this->get_settings_for_display();

        $gallery             = $settings['gallery'];
        $gap                 = intval( $settings['gap']['size'] );
        $breakpoint          = intval( $settings['mobile_breakpoint']['size'] );
        $link_to             = $settings['link_to'];
        $overlay_enabled     = $settings['overlay_enabled'] === 'yes';
        $overlay_title       = $settings['overlay_title'];
        $overlay_description = $settings['overlay_description'];
        $overlay_animation   = $settings['overlay_animation'];
        $animation_duration  = isset( $settings['overlay_animation_duration']['size'] ) ? intval( $settings['overlay_animation_duration']['size'] ) : 300;
        $vertical_position   = $settings['vertical_position'];
        $image_hover_anim    = $settings['image_hover_animation'];
        $image_anim_duration = isset( $settings['image_animation_duration']['size'] ) ? intval( $settings['image_animation_duration']['size'] ) : 400;

        if ( empty( $gallery ) ) {
            if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
                echo '<p style="padding:20px;text-align:center;color:#aaa;">Add images to display the gallery.</p>';
            }
            return;
        }

        $images = array();
        foreach ( $gallery as $item ) {
            $id   = intval( $item['id'] );
            $meta = wp_get_attachment_metadata( $id );
            if ( ! $meta ) { continue; }
            $images[] = array(
                'id'     => $id,
                'url'    => wp_get_attachment_image_url( $id, 'full' ),
                'width'  => isset( $meta['width'] )  ? intval( $meta['width'] )  : 1,
                'height' => isset( $meta['height'] ) ? intval( $meta['height'] ) : 1,
                'alt'    => get_post_meta( $id, '_wp_attachment_image_alt', true ),
                'page'   => get_attachment_link( $id ),
            );
        }

        if ( empty( $images ) ) { return; }

        // Nominal container width — JS scales from this to actual width
        $nominal_width = 1000;
        $rows          = $this->group_images( $images );
        $widget_id     = $this->get_id();

        $gallery_classes  = 'lw-tiled-gallery';
        if ( $image_hover_anim === 'zoom_in' ) {
            $gallery_classes .= ' lw-gallery--zoom-in';
        }

        $overlay_classes  = 'lw-overlay';
        $overlay_classes .= ' lw-overlay--' . esc_attr( $overlay_animation );
        $overlay_classes .= ' lw-overlay--' . esc_attr( $vertical_position );

        $overlay_style = $overlay_animation !== 'none' ? 'transition-duration:' . $animation_duration . 'ms;' : '';
        $img_style     = $image_hover_anim === 'zoom_in' ? 'transition: transform ' . $image_anim_duration . 'ms ease;' : '';

        echo '<div class="' . esc_attr( $gallery_classes ) . '"';
        echo ' data-original-width="' . esc_attr( $nominal_width ) . '"';
        echo ' data-gap="'            . esc_attr( $gap )            . '"';
        echo ' data-breakpoint="'     . esc_attr( $breakpoint )     . '"';
        echo ' id="lw-tg-'            . esc_attr( $widget_id )      . '"';
        echo '>';

        foreach ( $rows as $groups ) {
            $dims = $this->calculate_row_dimensions( $groups, $nominal_width, $gap );

            echo '<div class="lw-tiled-row"';
            echo ' data-original-width="'  . esc_attr( $nominal_width )      . '"';
            echo ' data-original-height="' . esc_attr( $dims['row_height'] ) . '"';
            echo '>';

            foreach ( $groups as $g_index => $group ) {
                $group_w     = $dims['group_widths'][ $g_index ];
                $group_class = count( $group ) > 1 ? 'lw-tiled-group lw-tiled-group--stack' : 'lw-tiled-group lw-tiled-group--single';

                // Calculate individual image heights within the group
                $img_height = count( $group ) > 1
                    ? (int) round( ( $dims['row_height'] - $gap * ( count( $group ) - 1 ) ) / count( $group ) )
                    : $dims['row_height'];

                echo '<div class="' . esc_attr( $group_class ) . '"';
                echo ' data-original-width="'  . esc_attr( $group_w )              . '"';
                echo ' data-original-height="' . esc_attr( $dims['row_height'] )   . '"';
                echo '>';

                foreach ( $group as $img_index => $image ) {
                    $this->render_item(
                        $image, $link_to,
                        $group_w, $img_height,
                        $overlay_classes, $overlay_style,
                        $overlay_title, $overlay_description,
                        $overlay_enabled, $img_style
                    );
                }

                echo '</div>';
            }

            echo '</div>';
        }

        echo '</div>';
    }

    private function render_item( $image, $link_to, $item_w, $item_h, $overlay_classes, $overlay_style, $overlay_title, $overlay_description, $overlay_enabled, $img_style ) {
        echo '<div class="lw-tiled-item"';
        echo ' data-original-width="'  . esc_attr( $item_w ) . '"';
        echo ' data-original-height="' . esc_attr( $item_h ) . '"';
        echo '>';

        $open_tag  = '';
        $close_tag = '';

        if ( $link_to === 'lightbox' ) {
            $open_tag  = '<a href="' . esc_url( $image['url'] ) . '" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="lw-tg">';
            $close_tag = '</a>';
        } elseif ( $link_to === 'attachment_page' ) {
            $open_tag  = '<a href="' . esc_url( $image['page'] ) . '">';
            $close_tag = '</a>';
        } elseif ( $link_to === 'media_file' ) {
            $open_tag  = '<a href="' . esc_url( $image['url'] ) . '">';
            $close_tag = '</a>';
        }

        echo wp_kses_post( $open_tag );

        echo '<img src="' . esc_url( $image['url'] ) . '"';
        echo ' width="'   . esc_attr( $image['width'] )  . '"';
        echo ' height="'  . esc_attr( $image['height'] ) . '"';
        echo ' alt="'     . esc_attr( $image['alt'] )    . '"';
        echo ' data-original-width="'  . esc_attr( $item_w ) . '"';
        echo ' data-original-height="' . esc_attr( $item_h ) . '"';
        if ( $img_style ) {
            echo ' style="' . esc_attr( $img_style ) . '"';
        }
        echo ' loading="lazy">';
echo wp_kses_post( $close_tag );
        $title_text  = $this->get_overlay_text( $overlay_title, $image['id'] );
        $desc_text   = $this->get_overlay_text( $overlay_description, $image['id'] );
        $has_content = $title_text || $desc_text;

        if ( $overlay_enabled || $has_content ) {
            echo '<div class="' . esc_attr( $overlay_classes ) . '"';
            if ( $overlay_style ) {
                echo ' style="' . esc_attr( $overlay_style ) . '"';
            }
            echo '>';
            if ( $has_content ) {
                echo '<div class="lw-overlay-content">';
                if ( $title_text ) {
                    echo '<div class="lw-overlay-title">' . esc_html( $title_text ) . '</div>';
                }
                if ( $desc_text ) {
                    echo '<div class="lw-overlay-description">' . esc_html( $desc_text ) . '</div>';
                }
                echo '</div>';
            }
            echo '</div>';
        }

        
        echo '</div>';
    }
}
