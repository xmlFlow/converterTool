<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita0b82bb6ee15d157537935a1bf6ab496
{
    public static $prefixLengthsPsr4 = array (
        'd' =>
        array (
            'docx2tei\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'docx2tei\\' =>
        array (
            0 => __DIR__ . '/../..' . '/src/docx2tei',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita0b82bb6ee15d157537935a1bf6ab496::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita0b82bb6ee15d157537935a1bf6ab496::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
