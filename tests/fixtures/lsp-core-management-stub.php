<?php
/**
 * Stub kelas Core untuk uji PH-01 (kondisi "Core tersedia").
 *
 * Dipisahkan dari berkas uji agar tidak ada konstruksi dinamis; di-require hanya
 * pada skenario C2 ketika kelas asli belum ada.
 */

if ( ! class_exists( 'LSP_Core_Management' ) ) {
    class LSP_Core_Management {
        public static function get_skema_skkni_data( $id ) {
            return array();
        }
    }
}
