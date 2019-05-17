<html>
<head>
<title>Buy Products</title>
<link rel="stylesheet" type="text/css" href="buy.css"/>
</head>
<body>
<div class="basket"><b>
Shopping Basket:</b><br/>
<div class="table">
<?php
 session_start();
 if(!empty($_GET['delete'])){
	 unset($_SESSION['basket'][$_GET['delete']]);
 }
 if(!empty($_GET['clear'])){
	 unset($_SESSION['basket']);
	 unset($_SESSION['temp']);
 }
 if(!empty($_GET['buy'])){
	 $_SESSION['basket'][$_GET['buy']] = array($_GET['buy'],$_SESSION['temp'][$_GET['buy']][0],$_SESSION['temp'][$_GET['buy']][1]);
 }
 $total = 0;
 if(isset($_SESSION['basket'])){
	foreach ($_SESSION['basket'] as $item){
		echo '<div class="basket_div"><label class="item_name">'.$item[1].'</label><label class="price1">'.$item[2].'$</label><label class="delete"><a href="buy.php?delete='.$item[0].'">Delete</a></label></div>';
		$total = $total + (float) $item[2];
	}
 }
?>
</div>
<br/><b>
Total: <?php echo $total."$"; ?></b><br/>
<form action="buy.php" method="GET" class="basket">
<input type="hidden" name="clear" value="1"/>
<input type="submit" class="clrbtn" value="Empty Basket"/>
</form>
<br/>
</div>
<?php
$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=72&showAllDescendants=true');
$xml = new SimpleXMLElement($xmlstr);
?>

<form action="buy.php" method="GET" class="search_bar">
<fieldset><legend>Find products:</legend>
<label>Category: 
<select name="category" value="1717">
<?php 
echo "<option value='".$xml->category['id']."'>".$xml->category->name."</option>";
echo "<optgroup label='".$xml->category->name.":'>";
foreach ($xml->category->categories->category as $category){
	echo "<option value='".$category['id']."'>".$category->name."</option>";
	echo "<optgroup label='".$category->name.":'>";
	foreach ($category->categories->category as $subcategory){	
		echo "<option value='".$subcategory['id']."'>".$subcategory->name."</option>";
	}
	echo "</optgroup>";
}  
echo "</optgroup>";
?>
</select>
</label>
<label>Search keywords: <input type="text" name="search" value="<?php if(isset($_GET['search'])){ echo $_GET['search'];}?>"/></label>
<input type="submit" value="Search" class="srchbtn"/>
</fieldset>
</form>
<br/>
<?php
if (!empty($_GET['category'])&&!empty($_GET['search'])){
	$xmlstr = file_get_contents('http://sandbox.api.shopping.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId='.$_GET['category'].'&keyword='.urlencode($_GET['search']).'&numItems=20');
	$xml = new SimpleXMLElement($xmlstr);
	//print_r($xml);
	if ($xml->categories['returnedCategoryCount']!="0"){
		echo "<div>";
		foreach ($xml->categories->category->items->offer as $offer){
			$_SESSION['temp'][(string)$offer['id']] = array((string)$offer->name,(string)$offer->basePrice); 
			echo '<div class="item"><a href="buy.php?buy='.$offer['id'].'"><label class="name">'.$offer->name.'</label></a><label class="price">'.$offer->basePrice.'$</label><label class="desc">'.$offer->description.'</label></div>';
		}
		echo "</div>";
	}
}
?>
</body>
</html>
