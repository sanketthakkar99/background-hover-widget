<?php
namespace BHW;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class Widget_Background_Hover extends Widget_Base {

    public function get_name() {
        return 'bhw-background-hover';
    }

    public function get_title() {
        return __( 'Background Hover Widget', 'background-hover-widget' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_style_depends() {
        return [ 'bhw-frontend' ];
    }

    public function get_script_depends() {
        return [ 'bhw-frontend' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_items',
            [
                'label' => __( 'Items', 'background-hover-widget' ),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => __( 'Title', 'background-hover-widget' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Item title' , 'background-hover-widget' ),
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => __( 'Description', 'background-hover-widget' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __( 'Item description', 'background-hover-widget' ),
            ]
        );

        $repeater->add_control(
            'bg_image',
            [
                'label' => __( 'Background Image', 'background-hover-widget' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [ 'url' => '' ],
            ]
        );

        $this->add_control(
            'items',
            [
                'label' => __( 'Repeater Items', 'background-hover-widget' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'default' => [
                    [ 'title' => __( 'Item 1', 'background-hover-widget' ) ],
                    [ 'title' => __( 'Item 2', 'background-hover-widget' ) ],
                ],
            ]
        );

        $this->end_controls_section();

        // Style section for height and overlay
        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Style', 'background-hover-widget' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'height',
            [
                'label' => __( 'Widget Height', 'background-hover-widget' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [ 'min' => 100, 'max' => 1200 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 420 ],
                'selectors' => [
                    '{{WRAPPER}} .bhw-widget' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => __( 'Overlay Color (optional)', 'background-hover-widget' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bhw-bg::after' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $items = isset( $settings['items'] ) && is_array( $settings['items'] ) ? array_slice( $settings['items'], 0, 5 ) : [];

        // Default background
        $default_bg = '';
        foreach ( $items as $it ) {
            if ( ! empty( $it['bg_image']['id'] ) ) {
                $default_bg = wp_get_attachment_image_url( $it['bg_image']['id'], 'full' );
                break;
            } elseif ( ! empty( $it['bg_image']['url'] ) ) {
                $default_bg = esc_url( $it['bg_image']['url'] );
                break;
            }
        }

        $unique_id = 'bhw-' . substr( md5( wp_rand() ), 0, 8 );

        wp_enqueue_style( 'bhw-frontend' );
        wp_enqueue_script( 'bhw-frontend' );

        ?>
        <div id="<?php echo esc_attr( $unique_id ); ?>" class="bhw-wrapper">
            <div class="bhw-widget elementor-widget-container">
                <div class="bhw-bg" style="background-image: url('<?php echo esc_url( $default_bg ); ?>');"></div>
                <div class="bhw-content">
                    <ul class="bhw-list">
                        <?php foreach ( $items as $item ) :
                            $title = isset( $item['title'] ) ? $item['title'] : '';
                            $desc = isset( $item['description'] ) ? $item['description'] : '';
                            $bg_url = '';
                            if ( ! empty( $item['bg_image']['id'] ) ) {
                                $bg_url = wp_get_attachment_image_url( $item['bg_image']['id'], 'full' );
                            } elseif ( ! empty( $item['bg_image']['url'] ) ) {
                                $bg_url = $item['bg_image']['url'];
                            }
                            ?>
                            <li class="bhw-item" data-bhw-bg="<?php echo esc_attr( $bg_url ); ?>" style="width: <?php echo (count($items) > 0 ? (100 / count($items)) : 100) . '%'; ?>;">
                                <div class="bhw-item-inner">
                                    <h4 class="bhw-item-title"><?php echo esc_html( $title ); ?></h4>
                                    <div class="bhw-item-desc"><?php echo esc_html( $desc ); ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }

    protected function _content_template() {
        ?>
        <#
        var items = settings.items || [];
        var defaultBg = '';
        if ( items.length ) {
            for ( var i = 0; i < items.length; i++ ) {
                if ( items[i].bg_image && items[i].bg_image.url ) { defaultBg = items[i].bg_image.url; break; }
            }
        }
        #>
        <div class="bhw-wrapper">
            <div class="bhw-widget">
                <div class="bhw-bg" style="background-image: url({{ defaultBg }});"></div>
                <div class="bhw-content">
                    <ul class="bhw-list">
                        <# _.each( items.slice(0,5), function( item ) { #>
                            <li class="bhw-item" data-bhw-bg="{{ item.bg_image.url }}">
                                <div class="bhw-item-inner">
                                    <h4 class="bhw-item-title">{{{ item.title }}}</h4>
                                    <div class="bhw-item-desc">{{{ item.description }}}</div>
                                </div>
                            </li>
                        <# } ); #>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
}