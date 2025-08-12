<?php
use Dompdf\Dompdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ExportController {
    public function pdf(){
        $slug=$_GET['slug']??''; $pdo=require __DIR__.'/../db.php';
        $st=$pdo->prepare("SELECT title,excerpt,content_md FROM posts WHERE slug=? LIMIT 1"); $st->execute([$slug]);
        $post=$st->fetch(); if(!$post){ http_response_code(404); exit('Not found'); }

        $html = '<h1>'.htmlspecialchars($post['title']).'</h1>';
        if(!empty($post['excerpt'])) $html .= '<p style="color:#555">'.htmlspecialchars($post['excerpt']).'</p>';
        $Parsedown = new Parsedown(); $Parsedown->setSafeMode(true);
        $html .= $Parsedown->text($post['content_md'] ?? '');

        $dompdf=new Dompdf(); $dompdf->loadHtml($html,'UTF-8'); $dompdf->setPaper('A4'); $dompdf->render();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$slug.'.pdf"');
        echo $dompdf->output();
    }
  // app/controllers/ExportController.php
    public function docx(){
        $slug = $_GET['slug'] ?? '';
        $pdo  = require __DIR__.'/../db.php';
        $st   = $pdo->prepare("SELECT title,content_md FROM posts WHERE slug=? LIMIT 1");
        $st->execute([$slug]);
        $post = $st->fetch(); if(!$post){ http_response_code(404); exit('Not found'); }

        $word    = new \PhpOffice\PhpWord\PhpWord();
        $section = $word->addSection();
        $section->addTitle($post['title'], 1);
        foreach (preg_split("~\R\R+~", $post['content_md'] ?? '') as $para) {
            $section->addText($para);
            $section->addTextBreak();
        }

        // Write to a temp file so we can set Content-Length
        $tmp = tempnam(sys_get_temp_dir(), 'docx_');
        \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007')->save($tmp);

        $filename = ($slug ?: 'document') . '.docx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($tmp));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        // absolutely no output before this point:
        readfile($tmp);
        @unlink($tmp);
        exit;
    }
}
