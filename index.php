<?php
	$dsn = 'mysql:host=localhost;dbname=BookCatalog';
	$username = 'php';
	$password = 'php';
	$db = new PDO($dsn, $username, $password);

	$query = "SELECT COURSE.courseID, COURSE.courseTitle, COURSE.credit, COURSEBOOK.book, BOOK.bookTitle, BOOK.price, BOOK.isbn13 FROM `COURSE`
						LEFT JOIN COURSEBOOK
						ON COURSE.courseID=COURSEBOOK.course
						INNER JOIN BOOK
						ON COURSEBOOK.book=BOOK.isbn13
						";

	$countQuery = "SELECT
									COUNT(BOOK.isbn13) AS total
								FROM
									`COURSE`
								LEFT JOIN
									COURSEBOOK ON COURSE.courseID = COURSEBOOK.course
								INNER JOIN
									BOOK ON COURSEBOOK.book = BOOK.isbn13";

	$statement2 = $db->prepare($countQuery);
	$statement2->execute();
	$count = $statement2->fetch();

	$pagination = ceil($count['total'] / 6);

	if (isset($_GET['price'])) {
		$order = $_GET['price'];
		$price = $order;
		$query .= "ORDER BY BOOK.price $order ";
	}

	if (isset($_GET['course'])) {
		$order = $_GET['course'];
		$course = $order;
		$query .= "ORDER BY COURSE.courseID $order ";
	}

	$query .= "LIMIT :offset, 6";

	$statement = $db->prepare($query);
	$statement->bindParam(':offset', $offset, PDO::PARAM_INT);

	if (isset($_GET['offset'])) {
		$offset = intval($_GET['offset']);
	} else {
		$offset = 0;
	}



	$statement->execute();

	$products = $statement->fetchAll(PDO::FETCH_ASSOC);
	$statement->closeCursor();
	$statement2->closeCursor();

	error_reporting(0);
?>
<!DOCTYPE>
<html>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="styles.css">
  <title>Project 1 | Book Catalog</title>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Book Catalog</a></h1>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>Course # <br/>( <a href="index.php?course=asc"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a> | <a href="index.php?course=desc"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></a> )</th>
					<th>Course Title</th>
					<th>Book Image</th>
					<th>Book Title</th>
					<th>Price   <br/>( <a href="index.php?price=asc"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a> | <a href="index.php?price=desc"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></a> )</th>
				</tr>
			</thead>
			<tbody>
				<?php $combine = null; ?>
				<?php foreach($products as $key => $product) { ?>

					<tr>
						<?php
							if (isset($combine)) {
								$combine = null;
								continue;
							}
						?>
						<td><a href="http://www.cpp.edu/~cba/computer-information-systems/curriculum/courses.shtml"><?php echo $product['courseID'] ?></a></td>
						<td><?php echo $product['courseTitle'] ?> (<?php echo $product['credit'] ?>)</td>
						<?php
							$next = $products[$key + 1];
							if ($next['courseID'] == $product['courseID']) {
								// print combined img, bookTitle, and price
								echo "<td><a href=\"book-detail.php?isbn=" . $product['isbn13'] . "\"\"><img src=images/" . $product['isbn13'] . ".jpg></a><br/><a href=\"book-detail.php?isbn=" . $next['isbn13'] . "\"\"><img src=images/" . $next['isbn13'] . ".jpg></a></td>";
								echo "<td>" . $product['bookTitle'] . "<br/>" . $next['bookTitle'] . "</td>";
								echo "<td>$" . $product['price'] . "<br/>$" . $next['price'] . "</td>";
								$combine = "combined";
							} else {
								echo "<td><a href=\"book-detail.php?isbn=" . $product['isbn13'] . "\"\"><img src=images/" . $product['isbn13'] . ".jpg></a></td>";
								echo "<td>" . $product['bookTitle'] . "</td>";
								echo "<td>$" . $product['price'] . "</td>";
							}
						 ?>

					</tr>
				<?php } ?>
			</tbody>
		</table>
		<nav>
			<ul class="pagination pagination-lg">
				<li class="<?php if ($offset == 0) echo "disabled" ?>">
					<a href="index.php?offset=<?php echo ($offset - 6) ?>" aria-label="Previous">
						<span aria-hidden="true">&laquo;</span>
					</a>
				</li>
				<?php
					if (isset($price)) {
						for ($i = 1; $i <= $pagination; $i++) {
							if (($offset / 6) + 1 == $i) {
								echo "<li class=\"active\"><a href=\"index.php?price=" . $price . "&offset=" . (($i - 1) * 6) . "\">" . $i . "</a></li>";
							} else {
								echo "<li><a href=\"index.php?price=" . $price . "&offset=" . (($i - 1) * 6) . "\">" . $i . "</a></li>";
							}
						}
					} else if (isset($course)) {
						for ($i = 1; $i <= $pagination; $i++) {
							if (($offset / 6) + 1 == $i) {
								echo "<li class=\"active\"><a href=\"index.php?course=" . $course . "&offset=" . (($i - 1) * 6) . "\">" . $i . "</a></li>";
							} else {
								echo "<li><a href=\"index.php?course=" . $course . "&offset=" . (($i - 1) * 6) . "\">" . $i . "</a></li>";
							}
						}
					} else {
						for ($i = 1; $i <= $pagination; $i++) {
							if(($offset / 6) + 1 == $i) {
								echo "<li class=\"active\"><a href=\"index.php?offset=" . (($i - 1) * 6) . "\">" . $i . "</a></li>";
							} else {
								echo "<li><a href=\"index.php?offset=" . (($i - 1) * 6) . "\">" . $i . "</a></li>";
							}
						}
					}

				?>
				<li class="<?php if ($offset + 6 > $count['total']) echo "disabled" ?>">
					<a href="index.php?offset=<?php echo ($offset + 6) ?>" aria-label="Next">
						<span aria-hidden="true">&raquo;</span>
					</a>
				</li>
			</ul>
		</nav>
	</div>


	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>
