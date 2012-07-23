<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Bootstrap, for ${born-properties.lib_name}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<?=$this->_includeBlock( 'Styles' );?>
	<body>

		<?=$this->_includeTemplate('/shared/header.tpl', true)?>
		<?=$this->_includeTemplate('/shared/menu.tpl', true)?>

		<div class="container">

			<?=$this->_includeTemplate($this->_getViewTemplate())?>

			<?=$this->_includeTemplate('/shared/footer.tpl', true)?>

		</div>
		<!-- /container -->

		<!-- Le javascript
    	================================================== -->
    	<!-- Placed at the end of the document so the pages load faster -->
		<?=$this->_includeBlock( 'Scripts' );?>
	</body>
</html>