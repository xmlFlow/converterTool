<?php



require_once (__DIR__ . "/vendor/autoload.php");

use docx2tei\DOCXArchive;
use docx2tei\structure\Document;

$inputPath = null;
$outputPath = null;
if ($argc == 3) {
	$inputPath = $argv[1];
	$outputPath = $argv[2];
} else {
	throw new InvalidArgumentException("requires valid input and output paths" . "\n" .
		"Basic usage: php docxtotei.php [path/to/file.docx or path/to/input/dir] [path/to/output/file.xml or path/to/output/dir]" ."\n");
}

$inputPathParts = pathinfo($inputPath);
if (array_key_exists("extension", $inputPathParts) && $inputPathParts["extension"] == "docx") {
	$inputs["singleFile"] = $inputPath;
} elseif (is_dir($inputPath)) {
	$inputs = scandir($inputPath);
} else {
	throw new UnexpectedValueException("the input must be a file with extension .docx or existing directory");
}

$outputPathParts = pathinfo($outputPath);

if (!array_key_exists("extension", $outputPathParts)) {
	$outputDir = $outputPath;
} else {
	$outputDir = $outputPathParts["dirname"];
}

$outputDir = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

if (!is_dir($outputDir)) {
	mkdir($outputDir, 0777, true);
}

if (array_key_exists("singleFile", $inputs)) {
	writeOutput($inputPath, $outputPathParts, $inputPathParts, $outputDir, false);
} else {
	foreach ($inputs as $input) {
		$inputFilePath = rtrim($inputPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $input;
		$inputFilePathParts = pathinfo($inputFilePath);
		if (array_key_exists("extension", $inputFilePathParts) && $inputFilePathParts["extension"] == "docx") {
			writeOutput($inputFilePath, $outputPathParts, $inputFilePathParts, $outputDir, true);
		}
	}
}


function writeOutput(string $inputFilePath, array $outputPathParts, array $inputPathParts, string $outputDir, bool $isDir): void
{
	$docxArchive = new DOCXArchive($inputFilePath);
	$structuredXML = new Document($docxArchive);
	$teiDocument = new docx2tei\tei\TEIDocument($structuredXML);

	if (array_key_exists("extension", $outputPathParts) && !$isDir) {
		$filename = $outputPathParts["filename"];
	} else {
		$filename = $inputPathParts["filename"];
	}

	if (!$isDir) {
        $filePath = $outputDir . $filename . ".xml";
        //$structuredXML->getTeiFile($filePath);
        $teiDocument->getDocument($filePath);
        $docxArchive->getMediaFiles($outputDir);
	} else {
		if (!is_dir($outputDir . $filename)) {
			mkdir($outputDir . $filename);
		}
        $dirFilePath = $outputDir . $filename . DIRECTORY_SEPARATOR . $filename . ".xml";
        //$structuredXML->getTeiFile($dirFilePath);
        $teiDocument->getDocument($dirFilePath);
		$docxArchive->getMediaFiles($outputDir . $filename . DIRECTORY_SEPARATOR);
	}
}
