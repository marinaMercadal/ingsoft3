<?php

if (!defined('ABSPATH')) {
    exit;
}

class MS_Donaciones_About {

    public static function init() {

        add_action(
            'admin_menu',
            [__CLASS__, 'register_menu'],
            20
        );

    }

    public static function register_menu() {

        add_submenu_page(

            'ms-donaciones',

            'Equipo',

            'Equipo',

            'manage_options',

            'ms-donaciones-equipo',

            [__CLASS__, 'render']

        );

    }

    public static function get_contributors() {

        return [

            [
                'nombre' => 'Agustin Kloster',
                'mail' => 'agustinkloster@uca.edu.ar'
            ],

            [
                'nombre' => 'Facundo del Valle',
                'mail' => 'facudelvalle@uca.edu.ar'
            ],

            [
                'nombre' => 'Bautista Alvarez Poli',
                'mail' => 'alvarezpolibautista@uca.edu.ar'
            ],

            [
                'nombre' => 'Ignacio Cardinale',
                'mail' => 'ignaciocardinale2004@uca.edu.ar'
            ],

            [
                'nombre' => "Bautista D'Imperio",
                'mail' => 'bautistadimperio@uca.edu.ar'
            ],

            [
                'nombre' => 'Facundo Alonso',
                'mail' => 'facundoalonso@uca.edu.ar'
            ],

            [
                'nombre' => 'Martina Ruiz',
                'mail' => 'martinaruiz@uca.edu.ar'
            ],

            [
                'nombre' => 'Mateo Villanueva',
                'mail' => 'mateovillanueva@uca.edu.ar'
            ],

            [
                'nombre' => 'Bautista Ubiría',
                'mail' => 'bautistaubiria@uca.edu.ar'
            ],

            [
                'nombre' => 'Carolina Suarez',
                'mail' => 'mariasuarez0@uca.edu.ar'
            ],

            [
                'nombre' => 'Joaquin Ravenna',
                'mail' => 'joaquinravenna@uca.edu.ar'
            ],

            [
                'nombre' => 'Marina Mercadal',
                'mail' => 'marinamercadal@uca.edu.ar'
            ],

        ];

    }

    public static function render() {

        if (!current_user_can('manage_options')) {
            wp_die('No autorizado.');
        }

        $contributors =
            self::get_contributors();

        ?>

        <div class="wrap">

            <h1>

                MS Donaciones

            </h1>

            <p>

                Plugin desarrollado para
                <strong>

                    Módulo Sanitario

                </strong>

                por estudiantes de

                <strong>

                    Ingeniería en Informática UCA

                </strong>

            </p>

            <div
                style="
                    background:#fff;
                    padding:24px;
                    border:1px solid #ddd;
                    border-radius:8px;
                    margin-top:20px;
                    max-width:1000px;
                "
            >

                <h2>

                    Equipo

                </h2>

                <table
                    class="
                        widefat
                        striped
                    "
                >

                    <thead>

                        <tr>

                            <th>

                                Integrante

                            </th>

                            <th>

                                Contacto

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    foreach(
                        $contributors
                        as $c
                    ):

                    ?>

                        <tr>

                            <td>

                                <?php

                                echo esc_html(
                                    $c['nombre']
                                );

                                ?>

                            </td>

                            <td>

                                <a href="mailto:<?php echo esc_attr($c['mail']); ?>">

                                <?php

                                echo esc_html(
                                    $c['mail']
                                );

                                ?>

                                </a>

                            </td>

                        </tr>

                    <?php

                    endforeach;

                    ?>

                    </tbody>

                </table>

                <br>

                <h2>

                    Información

                </h2>

                <table
                    class="
                        widefat
                        striped
                    "
                    style="
                        max-width:700px;
                    "
                >

                    <tbody>

                        <tr>

                            <td>

                                Versión

                            </td>

                            <td>

                                <?php echo esc_html(MS_DONACIONES_VERSION); ?>

                            </td>

                        </tr>

                        <tr>

                            <td>

                                ONG

                            </td>

                            <td>

                                Módulo Sanitario

                            </td>

                        </tr>

                        <tr>

                            <td>

                                Universidad

                            </td>

                            <td>

                                UCA

                            </td>

                        </tr>

                        <tr>

                            <td>

                                Repositorio

                            </td>

                            <td>

                                <a
                                    href="https://github.com/Bautista-Poli/ingsoft3"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >

                                    GitHub

                                </a>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

        <?php

    }

}
