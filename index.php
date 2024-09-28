<?php

$arrayPdfs = [
    'cover.pdf' => 'Cover',
    'pdf1.pdf' => 'PDF 1',
    'pdf2.pdf' => 'PDF 2',
    'pdf3.pdf' => 'PDF 3',
    'pdf4.pdf' => 'PDF 4',
];
$pageNumber = 1;
$bookmarks = '';
$commandConcat = 'pdftk ';
foreach ($arrayPdfs as $pdf => $nameBookmark) {
    if (!file_exists($pdf)) {
        continue;
    }
    // Get number of pages
    $output = [];
    exec('pdftk ' . $pdf . ' dump_data | findstr NumberOfPages', $output);
    if (empty($output)) {
        continue;
    }
    $numberOfPages = str_replace('NumberOfPages: ', '', $output[0]) ?? 0;
    if ($numberOfPages > 0) {
        // Add pdf to command concat
        $commandConcat .= $pdf . ' ';
    }
    // Add bookmark
    $bookmarks .= 'BookmarkBegin
BookmarkTitle: ' . $nameBookmark . '
BookmarkLevel: 1
BookmarkPageNumber: ' . ($pageNumber) . '

';
    $pageNumber += $numberOfPages;
    echo "numberOfPages: " . $numberOfPages . "<br>";
}
// Generate bookmarks file with bookmarks
file_put_contents('bookmarks.txt', $bookmarks);
exec($commandConcat . 'cat output output.pdf');
exec('pdftk output.pdf update_info bookmarks.txt output output_final.pdf');
echo "<pre>";
echo $bookmarks;
echo "</pre>";
