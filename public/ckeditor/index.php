<?php header('Content-Type:text/html; charset=utf-8', true); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>AOC ::: Administração</title>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
        <link rel="stylesheet" href="js/ckeditor/samples/sample.css">
    </head>
    <body>

        <script src="js/ckeditor/ckeditor.js"></script>
        <textarea name="texto" id="texto"  rows="30" cols="100" style="width: 90%;" class="ckeditor"><p>texto aqui</p></textarea>
        <script type="text/javascript">
            CKEDITOR.replace('texto',
                    {
                        toolbar: 'Full',
                        allowedContent: true
                    });
        </script>
    </body>

</html>
