<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GeneratePosts extends Command
{
    protected $signature = 'posts:generate
                            {count=10 : Cantidad de posts a crear}
                            {--status=published : published, draft, rejected o random}
                            {--no-image : No generar imagenes}';

    protected $description = 'Crea posts de prueba con imagenes aleatorias generadas con GD';

    public function handle(): int
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->error('No hay usuarios. Ejecuta primero: php artisan db:seed');
            return self::FAILURE;
        }

        if (! $this->option('no-image') && ! function_exists('imagecreatetruecolor')) {
            $this->error('La extension GD no esta instalada (sudo apt install php-gd).');
            return self::FAILURE;
        }

        $count = max(1, (int) $this->argument('count'));
        $bar = $this->output->createProgressBar($count);

        for ($i = 0; $i < $count; $i++) {
            $status = $this->resolveStatus();

            Post::create([
                'user_id' => $users->random()->id,
                'title' => rtrim(fake()->sentence(random_int(4, 8)), '.'),
                'content' => fake()->paragraphs(random_int(3, 5), true),
                'image_path' => $this->option('no-image') ? null : $this->makeImage(),
                'is_published' => $status,
                'published_at' => $status === Post::STATUS_PUBLISHED
                    ? fake()->dateTimeBetween('-60 days', 'now')
                    : null,
                'rejection_reason' => $status === Post::STATUS_REJECTED
                    ? fake()->sentence()
                    : null,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Se crearon {$count} posts.");

        return self::SUCCESS;
    }

    private function resolveStatus(): int
    {
        return match ($this->option('status')) {
            'draft' => Post::STATUS_DRAFT,
            'rejected' => Post::STATUS_REJECTED,
            'random' => fake()->randomElement([
                Post::STATUS_DRAFT,
                Post::STATUS_PUBLISHED,
                Post::STATUS_REJECTED,
            ]),
            default => Post::STATUS_PUBLISHED,
        };
    }

    /**
     * Genera una imagen 1280x720 con degradado y formas aleatorias
     * y devuelve la ruta relativa dentro del disco "public".
     */
    private function makeImage(): string
    {
        $w = 1280;
        $h = 720;
        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, true);

        $c1 = [random_int(20, 235), random_int(20, 235), random_int(20, 235)];
        $c2 = [random_int(20, 235), random_int(20, 235), random_int(20, 235)];

        for ($y = 0; $y < $h; $y++) {
            $t = $y / ($h - 1);
            $color = imagecolorallocate(
                $img,
                (int) ($c1[0] + ($c2[0] - $c1[0]) * $t),
                (int) ($c1[1] + ($c2[1] - $c1[1]) * $t),
                (int) ($c1[2] + ($c2[2] - $c1[2]) * $t),
            );
            imageline($img, 0, $y, $w, $y, $color);
        }

        for ($i = 0, $n = random_int(8, 18); $i < $n; $i++) {
            $shape = imagecolorallocatealpha(
                $img,
                random_int(0, 255),
                random_int(0, 255),
                random_int(0, 255),
                random_int(70, 115),
            );
            $x = random_int(0, $w);
            $y = random_int(0, $h);
            $size = random_int(80, 420);

            if (random_int(0, 1)) {
                imagefilledellipse($img, $x, $y, $size, $size, $shape);
            } else {
                imagefilledrectangle($img, $x, $y, $x + $size, $y + (int) ($size * 0.6), $shape);
            }
        }

        ob_start();
        imagejpeg($img, null, 85);
        $data = ob_get_clean();
        imagedestroy($img);

        $path = 'posts/' . fake()->uuid() . '.jpg';
        Storage::disk('public')->put($path, $data);

        return $path;
    }
}
