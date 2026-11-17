<?php

namespace Database\Seeders;

use App\Enums\MenuCategory;
use App\Models\Material;
use App\Models\Menu;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

/**
 * Data induk awal dari brief pemilik (16 Nov 2026) dan sheet Data_Produk.
 *
 * Aman dijalankan berulang: data yang sudah ada (berdasarkan nama) tidak ditimpa,
 * sehingga perubahan yang dibuat admin lewat panel tetap terjaga.
 *
 * Harga & pembagian sengaja TIDAK diisi di sini: angka resmi harus diambil
 * dari sheet Data_Produk, bukan ditebak.
 */
class MasterDataSeeder extends Seeder
{
    /** Daftar menu resmi (brief bagian 19). */
    public const MENUS = [
        'Buah',
        'Kacang Sangrai',
        'Kacang Selai',
        'Coklat',
        'Keju', // di brief tertulis "keju"; di Sheets & desain "Keju"
        'Kacang Sangrai + Buah',
        'Coklat + Buah',
        'Keju + Buah',
        'Kacang Sangrai + Coklat',
        'Kacang Sangrai + Keju',
        'Coklat + Keju',
        'Coklat Keju + Buah',
        'Kacang Sangrai Coklat + Buah',
        'Coklat Keju + Coklat',
        'Kacang Sangrai Coklat + Coklat',
        'Coklat Keju + Kacang Sangrai',
        'Kacang Sangrai Coklat + Kacang Sangrai',
        'Coklat Keju + Keju',
        'Kacang Sangrai Coklat + Keju',
        'Coklat Keju',
        'Coklat Keju + Kacang Sangrai Coklat',
        'Kacang Sangrai Coklat',
        'Choco Crunchy',
        'Tiramisu Crunchy',
        'Cheese Crunchy',
        'Milk Crunchy',
        'Greentea Crunchy',
        'Hazelnut Crunchy',
        'Choco Crunchy Keju',
        'Tiramisu Crunchy Keju',
        'Cheese Crunchy Keju',
        'Milk Crunchy Keju',
        'Greentea Crunchy Keju',
        'Hazelnut Crunchy Keju',
        'Choco Crunchy + Tiramisu Crunchy',
        'Choco Crunchy + Cheese Crunchy',
        'Choco Crunchy + Milk Crunchy',
        'Choco Crunchy + Greentea Crunchy',
        'Choco Crunchy + Hazelnut Crunchy',
        'Tiramisu Crunchy + Cheese Crunchy',
        'Tiramisu Crunchy + Milk Crunchy',
        'Tiramisu Crunchy + Greentea Crunchy',
        'Tiramisu Crunchy + Hazelnut Crunchy',
        'Cheese Crunchy + Milk Crunchy',
        'Cheese Crunchy + Greentea Crunchy',
        'Cheese Crunchy + Hazelnut Crunchy',
        'Milk Crunchy + Greentea Crunchy',
        'Milk Crunchy + Hazelnut Crunchy',
        'Greentea Crunchy + Hazelnut Crunchy',
        'Choco Crunchy Keju + Tiramisu Crunchy Keju',
        'Choco Crunchy Keju + Cheese Crunchy Keju',
        'Choco Crunchy Keju + Milk Crunchy Keju',
        'Choco Crunchy Keju + Greentea Crunchy Keju',
        'Choco Crunchy Keju + Hazelnut Crunchy Keju',
        'Tiramisu Crunchy Keju + Cheese Crunchy Keju',
        'Tiramisu Crunchy Keju + Milk Crunchy Keju',
        'Tiramisu Crunchy Keju + Greentea Crunchy Keju',
        'Tiramisu Crunchy Keju + Hazelnut Crunchy Keju',
        'Cheese Crunchy Keju + Milk Crunchy Keju',
        'Cheese Crunchy Keju + Greentea Crunchy Keju',
        'Cheese Crunchy Keju + Hazellnut Crunchy Keju', // ejaan "Hazellnut" sesuai brief — perlu dikonfirmasi pemilik
        'Milk Crunchy Keju + Greentea Crunchy Keju',
        'Milk Crunchy Keju + Hazelnut Crunchy Keju',
        'Greentea Crunchy Keju + Hazelnut Crunchy Keju',
        'Cheese Milk Crunchy Kombinasi',
        'Cheese Choco Crunchy Kombinasi',
        'Cheese Milk Crunchy Keju Kombinasi',
        'Cheese Choco Crunchy Keju Kombinasi',
        'Kacang Selai + Buah',
        'Kacang Selai + Coklat',
        'Kacang Selai + Keju',
        'Kacang Selai Coklat + Keju',
        'Kacang Selai Coklat + Buah',
        'Kacang Selai Coklat + Coklat',
        'Coklat Keju + Kacang Selai',
        'Kacang Selai Coklat + Kacang Selai',
        'Coklat Keju + Kacang Selai Coklat',
        'Kacang Selai Coklat',
    ];

    /**
     * Daftar bahan resmi (brief bagian 19) => satuan.
     * Satuan hanya diisi bila terlihat di tangkapan layar Order_Bahan; sisanya diisi admin.
     */
    public const MATERIALS = [
        'Roti' => 'pcs',
        'Margarin Panggang' => null,
        'Margarin Oles' => null,
        'Kental Manis' => null,
        'Keju' => null,
        'Pasta Coklat' => null,
        'Meses' => null,
        'Kacang sangrai' => null,
        'Goldenfil Cheese Crunchy' => 'kg',
        'Goldenfil Choco Crunchy' => null,
        'Goldenfil Greentea Crunchy' => null,
        'Goldenfil Hazelnut Crunchy' => null,
        'Goldenfil Milk Crunchy' => null,
        'Goldenfil Tiramisu Crunchy' => null,
        'Selai Blueberry' => null,
        'Selai Strawberry' => null,
        'Gas' => null,
        'Keresek HD 24' => null,
        'Dus Kemasan' => null,
        'Bon' => null,
        'Sarung Tangan Plastik' => null,
        'Kupon' => null,
        'Sabun Cuci Piring' => null,
        'Tisu' => null,
        'Tanda Terimakasih' => null,
        'Kacang Selai' => null,
        'Meses 2' => null,
    ];

    /** [nama di layar, nilai persis di Google Sheets, tunai?] — urutan dari sheet Data_Produk. */
    public const PAYMENT_METHODS = [
        ['Tunai', 'Tunai', true],
        ['QRIS', 'QRIS', false],
        ['Transfer', 'Transfer', false],
        ['Shopee', 'Shopee', false],
        ['GoFood', 'Gofood', false],
        ['GrabFood', 'Grabfood', false],
    ];

    public function run(): void
    {
        foreach (self::MENUS as $name) {
            Menu::firstOrCreate(
                ['name' => $name],
                ['category' => MenuCategory::guessFromName($name), 'is_active' => true],
            );
        }

        foreach (self::MATERIALS as $name => $unit) {
            Material::firstOrCreate(
                ['name' => $name],
                ['unit' => $unit, 'is_active' => true],
            );
        }

        foreach (self::PAYMENT_METHODS as $order => [$name, $sheetValue, $isCash]) {
            PaymentMethod::firstOrCreate(
                ['sheet_value' => $sheetValue],
                ['name' => $name, 'is_cash' => $isCash, 'is_active' => true, 'sort_order' => $order + 1],
            );
        }
    }
}
