<html>
<head prefix="og: http://ogp.me/ns# product: http://ogp.me/ns/product#">
    <meta property="fb:app_id" content="<?php echo strip_tags($_REQUEST['fb:app_id']);?>">
    <meta property="og:url" content="<?php echo strip_tags(curPageURL());?>">
    <meta property="og:type" content="<?php echo strip_tags($_REQUEST['og:type']);?>">
    <meta property="og:title" content="<?php echo strip_tags($_REQUEST['og:title']);?>">
    <meta property="og:image" content="<?php echo strip_tags($_REQUEST['og:image']);?>">
    <meta property="og:description" content="<?php echo strip_tags($_REQUEST['og:description']);?>">
    <title>Product Name</title>
</head>
<body>
{$thebody}
</body>
</html>
