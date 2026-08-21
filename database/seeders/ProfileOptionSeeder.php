<?php

namespace Database\Seeders;

use App\Enums\Profile\ProfileOptionTypeEnum;
use App\Models\ProfileOption;
use Illuminate\Database\Seeder;

class ProfileOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProfileOption::query()->delete();

        $items = [
            // ================= 1. QIZIQISHLAR (INTEREST) =================
            // Sport va Fitnes
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Sport va Fitnes', 'name' => 'Futbol', 'icon' => '⚽', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Sport va Fitnes', 'name' => 'Yugurish', 'icon' => '🏃', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Sport va Fitnes', 'name' => 'Boks', 'icon' => '🥊', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Sport va Fitnes', 'name' => 'Yoga', 'icon' => '🧘', 'order' => 4],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Sport va Fitnes', 'name' => 'Suzish', 'icon' => '🏊', 'order' => 5],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Sport va Fitnes', 'name' => 'Tennis', 'icon' => '🎾', 'order' => 6],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Sport va Fitnes', 'name' => 'Velosiped', 'icon' => '🚴', 'order' => 7],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Sport va Fitnes', 'name' => 'Basketbol', 'icon' => '🏀', 'order' => 8],

            // Musiqa
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Musiqa', 'name' => 'Pop musiqa', 'icon' => '🎵', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Musiqa', 'name' => 'Rap & Hip-Hop', 'icon' => '🎤', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Musiqa', 'name' => 'Rock', 'icon' => '🎸', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Musiqa', 'name' => 'Jazz', 'icon' => '🎷', 'order' => 4],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Musiqa', 'name' => 'Klassika', 'icon' => '🎻', 'order' => 5],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Musiqa', 'name' => 'Lo-Fi & Chill', 'icon' => '🎧', 'order' => 6],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Musiqa', 'name' => 'Uzbek Pop', 'icon' => '📻', 'order' => 7],

            // Kino va Seriallar
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Kino va Seriallar', 'name' => 'Komediya', 'icon' => '😂', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Kino va Seriallar', 'name' => 'Fantastika', 'icon' => '🚀', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Kino va Seriallar', 'name' => 'Jangari', 'icon' => '💥', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Kino va Seriallar', 'name' => 'Drama', 'icon' => '🎭', 'order' => 4],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Kino va Seriallar', 'name' => 'Anime', 'icon' => '⛩️', 'order' => 5],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Kino va Seriallar', 'name' => 'Triller', 'icon' => '🥷', 'order' => 6],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Kino va Seriallar', 'name' => 'Marvel & DC', 'icon' => '🦸', 'order' => 7],

            // Ovqat va Ichimliklar
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Ovqat va Ichimliklar', 'name' => 'Qahva', 'icon' => '☕', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Ovqat va Ichimliklar', 'name' => 'Sushi', 'icon' => '🍣', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Ovqat va Ichimliklar', 'name' => 'Milliy taomlar', 'icon' => '🍲', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Ovqat va Ichimliklar', 'name' => 'Fast-Food', 'icon' => '🍔', 'order' => 4],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Ovqat va Ichimliklar', 'name' => 'Shirinliklar', 'icon' => '🍰', 'order' => 5],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Ovqat va Ichimliklar', 'name' => 'Choyxo\'rlik', 'icon' => '🍵', 'order' => 6],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Ovqat va Ichimliklar', 'name' => 'PP & Dieta', 'icon' => '🥗', 'order' => 7],

            // Tabiat va Sayohat
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Tabiat va Sayohat', 'name' => 'Sayohat', 'icon' => '✈️', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Tabiat va Sayohat', 'name' => 'Tog\' sayohati', 'icon' => '⛰️', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Tabiat va Sayohat', 'name' => 'Dengiz & Plyaj', 'icon' => '🏖️', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Tabiat va Sayohat', 'name' => 'Chodir & Kamping', 'icon' => '🏕️', 'order' => 4],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Tabiat va Sayohat', 'name' => 'Shahar sayri', 'icon' => '🏙️', 'order' => 5],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'Tabiat va Sayohat', 'name' => 'Road Trip', 'icon' => '🚗', 'order' => 6],

            // O'yinlar (Gaming)
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'O\'yinlar (Gaming)', 'name' => 'PlayStation', 'icon' => '🎮', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'O\'yinlar (Gaming)', 'name' => 'PUBG Mobile', 'icon' => '🔫', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'O\'yinlar (Gaming)', 'name' => 'CS2 / CS:GO', 'icon' => '🎯', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'O\'yinlar (Gaming)', 'name' => 'FIFA', 'icon' => '⚽', 'order' => 4],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'O\'yinlar (Gaming)', 'name' => 'Shaxmat', 'icon' => '♟️', 'order' => 5],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'O\'yinlar (Gaming)', 'name' => 'Stol o\'yinlari', 'icon' => '🎲', 'order' => 6],

            // San'at va IT
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'San\'at va IT', 'name' => 'Dasturlash', 'icon' => '💻', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'San\'at va IT', 'name' => 'UI/UX Dizayn', 'icon' => '🎨', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'San\'at va IT', 'name' => 'Kitob o\'qish', 'icon' => '📚', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'San\'at va IT', 'name' => 'Fotografiya', 'icon' => '📷', 'order' => 4],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'San\'at va IT', 'name' => 'Psixologiya', 'icon' => '🧠', 'order' => 5],
            ['type' => ProfileOptionTypeEnum::INTEREST, 'category' => 'San\'at va IT', 'name' => 'Biznes & Startap', 'icon' => '💼', 'order' => 6],


            // ================= 2. TANISHISHDAN MAQSAD (DATING_PURPOSE) =================
            ['type' => ProfileOptionTypeEnum::DATING_PURPOSE, 'category' => null, 'name' => 'Nikoh va oila', 'icon' => '💍', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::DATING_PURPOSE, 'category' => null, 'name' => 'Jiddiy munosabat', 'icon' => '❤️', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::DATING_PURPOSE, 'category' => null, 'name' => 'Muloqot & do\'stlik', 'icon' => '☕', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::DATING_PURPOSE, 'category' => null, 'name' => 'Hali aniq emas', 'icon' => '🤔', 'order' => 4],


            // ================= 3. TURMUSH TARZI (LIFESTYLE) =================
            // Chekish odati
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Chekish odati', 'name' => 'Chekmayman', 'icon' => '🚭', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Chekish odati', 'name' => 'Chekaman', 'icon' => '🚬', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Chekish odati', 'name' => 'Faqat kalyan', 'icon' => '💨', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Chekish odati', 'name' => 'Tashlash arafasidaman', 'icon' => '⏳', 'order' => 4],

            // Ichimliklar
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Ichimliklar', 'name' => 'Ichmayman', 'icon' => '🚫', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Ichimliklar', 'name' => 'Faqat bayramlarda', 'icon' => '🥂', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Ichimliklar', 'name' => 'Ba\'zan', 'icon' => '🍷', 'order' => 3],

            // Tungi hayot / Klublar
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Tungi hayot / Klublar', 'name' => 'Uyda tinch o\'tirish', 'icon' => '🏠', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Tungi hayot / Klublar', 'name' => 'Klublarga boraman', 'icon' => '🏢', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Tungi hayot / Klublar', 'name' => 'Ba\'zan boraman', 'icon' => '✨', 'order' => 3],

            // Sport bilan shug'ullanish
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Sport bilan shug\'ullanish', 'name' => 'Har kuni', 'icon' => '🥇', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Sport bilan shug\'ullanish', 'name' => 'Tez-tez (haftada 2–3)', 'icon' => '🥈', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Sport bilan shug\'ullanish', 'name' => 'Ba\'zan', 'icon' => '🥉', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::LIFESTYLE, 'category' => 'Sport bilan shug\'ullanish', 'name' => 'Hech qachon', 'icon' => '💤', 'order' => 4],


            // ================= 4. MEN HAQIMDA KO'PROQ (ABOUT_ME) =================
            // Ta'lim darajasi
            ['type' => ProfileOptionTypeEnum::ABOUT_ME, 'category' => 'Ta\'lim darajasi', 'name' => 'Maktab', 'icon' => '🏫', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::ABOUT_ME, 'category' => 'Ta\'lim darajasi', 'name' => 'Kollej / Litsey', 'icon' => '🎓', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::ABOUT_ME, 'category' => 'Ta\'lim darajasi', 'name' => 'Bakalavr (Oliy)', 'icon' => '🎓', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::ABOUT_ME, 'category' => 'Ta\'lim darajasi', 'name' => 'Magistratura', 'icon' => '📚', 'order' => 4],
            ['type' => ProfileOptionTypeEnum::ABOUT_ME, 'category' => 'Ta\'lim darajasi', 'name' => 'PhD / Doktorantura', 'icon' => '🔬', 'order' => 5],

            // Muloqot uslubi
            ['type' => ProfileOptionTypeEnum::ABOUT_ME, 'category' => 'Muloqot uslubi', 'name' => 'Yozishmalar (SMS)', 'icon' => '💬', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::ABOUT_ME, 'category' => 'Muloqot uslubi', 'name' => 'Qo\'ng\'iroqlar', 'icon' => '📞', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::ABOUT_ME, 'category' => 'Muloqot uslubi', 'name' => 'Ovozli xabarlar', 'icon' => '🎙️', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::ABOUT_ME, 'category' => 'Muloqot uslubi', 'name' => 'Uchrashuv & sayrlar', 'icon' => '🚶', 'order' => 4],


            // ================= 5. OILAVIY HOLATI (MARITAL_STATUS) =================
            ['type' => ProfileOptionTypeEnum::MARITAL_STATUS, 'category' => null, 'name' => 'Birinchi marta turmush qurilmoqda', 'icon' => '💍', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::MARITAL_STATUS, 'category' => null, 'name' => 'Ajrashgan', 'icon' => '💔', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::MARITAL_STATUS, 'category' => null, 'name' => 'Oilalik (Turmush qurgan)', 'icon' => '👨‍👩‍👧', 'order' => 3],


            // ================= 6. BILADIGAN TILLARI (LANGUAGE) =================
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'O\'zbek tili', 'icon' => '🇺🇿', 'order' => 1],
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'Rus tili', 'icon' => '🇷🇺', 'order' => 2],
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'Ingliz tili', 'icon' => '🇬🇧', 'order' => 3],
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'Qozoq tili', 'icon' => '🇰🇿', 'order' => 4],
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'Qirg\'iz tili', 'icon' => '🇰🇬', 'order' => 5],
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'Turk tili', 'icon' => '🇹🇷', 'order' => 6],
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'Tojik tili', 'icon' => '🇹🇯', 'order' => 7],
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'Arab tili', 'icon' => '🇸🇦', 'order' => 8],
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'Nemis tili', 'icon' => '🇩🇪', 'order' => 9],
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'Koreys tili', 'icon' => '🇰🇷', 'order' => 10],
            ['type' => ProfileOptionTypeEnum::LANGUAGE, 'category' => null, 'name' => 'Xitoy tili', 'icon' => '🇨🇳', 'order' => 11],
        ];

        foreach ($items as $item) {
            ProfileOption::create([
                'type' => $item['type']->value,
                'category' => $item['category'],
                'name' => $item['name'],
                'icon' => $item['icon'],
                'order' => $item['order'],
                'is_active' => true,
            ]);
        }
    }
}
