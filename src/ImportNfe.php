<?php

namespace AwaCode\ImportNfe;


/**
 * Class AwaCode ImportNfe
 *
 * @author Rhyann Carvalhais <https://github.com/rhyannc>
 * @package AwaCode\ImportNfe
 */
abstract class ImportNfe
{
    /** @var string */
    protected $path;

    /** @var resource */
    protected $file;

    /** @var string */
    protected $name;

    /** @var string */
    protected $ext;

    /** @var array */
    protected static $allowTypes = [];

    /** @var array */
    protected static $extensions = [];

   /**
     * @param string $uploadDir
     * @param string $fileTypeDir
     * @param bool $monthYearPath
     * @example $u = new Upload("storage/uploads", "importadas");
     */
   public function __construct(string $uploadDir, string $fileTypeDir, string $importDir, string $errorDir, bool $monthYearPath = true)
    {
        $this->dir($uploadDir);
        $this->dir("{$uploadDir}/{$importDir}");
        $this->dir("{$uploadDir}/{$errorDir}");
        $this->dir("{$uploadDir}/{$fileTypeDir}");

        $this->pathImport = "{$uploadDir}/{$importDir}";
        $this->pathError = "{$uploadDir}/{$errorDir}";
        $this->path = "{$uploadDir}/{$fileTypeDir}";

        if ($monthYearPath) {
            $this->path("{$uploadDir}/{$fileTypeDir}");
        }
    }


    /**
     * @param string $name
     * @return string
     */
    protected function name(string $name): string
    {
        $name = filter_var(mb_strtolower($name), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $formats = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        $replace = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyrr                                 ';
        $name = str_replace(
            ["-----", "----", "---", "--"],
            "-",
            str_replace(" ", "-", trim(strtr(utf8_decode($name), utf8_decode($formats), $replace)))
        );

        $this->name = "{$name}." . $this->ext;

        if (file_exists("{$this->path}/{$this->name}") && is_file("{$this->path}/{$this->name}")) {
            $this->name = "{$name}-" . time() . ".{$this->ext}";
        }
        return $this->name;
    }

    /**
     * @param string $dir
     * @param int $mode
     */
    protected function dir(string $dir, int $mode = 0755): void
    {
        if (!file_exists($dir) || !is_dir($dir)) {
            mkdir($dir, $mode, true);
        }
    }

    /**
     * @param string $path
     */
    protected function path(string $path): void
    {
        list($yearPath, $mothPath) = explode("/", date("Y/m"));

        $this->dir("{$path}/{$yearPath}");
        $this->dir("{$path}/{$yearPath}/{$mothPath}");
        $this->path = "{$path}/{$yearPath}/{$mothPath}";
    }

    /**
     * @param array $file
     */
    protected function ext(array $file): void
    {
        $this->ext = mb_strtolower(pathinfo($file['name'])['extension']);
    }

    /**
     * @param $inputName
     * @param $files
     * @return array
     */

    /**
     * @param $inputName
     * @param $files
     * @return array
     */
    public function multiple($inputName, $files): array
    {
        $gbFiles = [];
        $gbCount = count($files[$inputName]["name"]);
        $gbKeys = array_keys($files[$inputName]);

        for ($gbLoop = 0; $gbLoop < $gbCount; $gbLoop++):
            foreach ($gbKeys as $key):
                $gbFiles[$gbLoop][$key] = $files[$inputName][$key][$gbLoop];
            endforeach;
        endfor;

        return $gbFiles;
    }

}