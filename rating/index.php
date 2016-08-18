<!DOCTYPE html>
<head>
<link href="rating.css" rel="stylesheet" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="rating.js"></script>
<script language="javascript" type="text/javascript">
$(function() {
    $("#rating_star").ratio({
        starLength: '5',
        initialValue: '',
        imageDirectory: 'images',
    });
});

</script>

</head>
<body>
    <input name="rating" value="3" id="rating_star" type="hidden" />
</body>
</html>