<?php

namespace App\Services\SeoPage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SeoPageImageService
{
    /** Relative to `assets/`, the same convention FileManageTrait uses. */
    public const DIRECTORY = 'general/images/seo-pages';

    /** Extensions allowed once the file's detected content type is known. */
    public const ALLOWED_EXTENSIONS = ['jpg', 'png', 'webp'];

    /**
    * Saves the upload as `{slug}.{ext}` (or `{slug}-2.{ext}`, ...) in an optional slug hierarchy,
    * and returns the path for `asset()`.
     *
     * The extension is derived from the upload's detected content, not the client-supplied file name.
     * Callers must still validate the upload (size, `image` rule, etc.) before calling this method.
     *
     * @throws \InvalidArgumentException if the detected content type isn't an allowed image type
     */
    public function store(UploadedFile $file, string $slug, ?string $subdirectory = null): string
    {
        $extension = $file->guessExtension();
        $extension = $extension === 'jpeg' ? 'jpg' : strtolower((string) $extension);

        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new \InvalidArgumentException('Unsupported image type.');
        }

        $segments = array_filter(array_map(
            static fn (string $segment): string => Str::slug($segment),
            explode('/', trim((string) $subdirectory, '/'))
        ));
        $subdirectory = implode('/', $segments);
        $relativeDirectory = self::DIRECTORY.($subdirectory === '' ? '' : '/'.$subdirectory);
        $directory = base_path('assets/'.$relativeDirectory);
        File::ensureDirectoryExists($directory);

        $name = "{$slug}.{$extension}";
        $suffix = 2;
        while (file_exists("{$directory}/{$name}")) {
            $name = "{$slug}-{$suffix}.{$extension}";
            $suffix++;
        }

        $file->move($directory, $name);

        return $relativeDirectory.'/'.$name;
    }

    public function delete(?string $path): void
    {
        if ($path !== null && is_file(base_path('assets/'.$path))) {
            unlink(base_path('assets/'.$path));
        }
    }
}
