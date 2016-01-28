<?php
	$isbn = $_GET['isbn'];

	$dsn = 'mysql:host=localhost;dbname=BookCatalog';
	$username = 'php';
	$password = 'php';
	$db = new PDO($dsn, $username, $password);

	$query = "SELECT
							BOOK.isbn13,
							BOOK.bookTitle,
							BOOK.price,
							BOOK.edition,
							BOOK.publishDate,
							BOOK.length,
							BOOK.description,
							COURSE.*,
							PUBLISHER.publisher
						FROM
							BOOK
						LEFT JOIN
							COURSEBOOK ON BOOK.isbn13 = COURSEBOOK.book
						INNER JOIN
							COURSE ON COURSE.courseID = COURSEBOOK.course
						INNER JOIN
							PUBLISHER ON BOOK.publisher = PUBLISHER.publisherID
						WHERE
							BOOK.isbn13 = :isbn";

	$query2 = "SELECT
							AUTHOR.firstName,
							AUTHOR.lastName
						FROM
							BOOK
						LEFT JOIN
							AUTHORBOOK ON BOOK.isbn13 = AUTHORBOOK.book
						INNER JOIN
							AUTHOR ON AUTHORBOOK.author = AUTHOR.authorID
						WHERE
							BOOK.isbn13 = :isbn";

	$statement = $db->prepare($query);
	$statement2 = $db->prepare($query2);
	$statement->bindValue(':isbn', $isbn);
	$statement2->bindValue(':isbn', $isbn);
	$statement->execute();
	$statement2->execute();

	$book = $statement->fetch();
	$author = $statement2->fetchAll();
	$statement->closeCursor();
	$statement2->closeCursor();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<title>Book Detail</title>
</head>
<body>

	<div class="container">
		<h1><a href="index.php">Book Catalog</a></h1>
		<div class="panel panel-default">
			<div class="panel-body">
				<table>
					<tbody>
						<tr>
							<td><img src="images/<?php echo $book['isbn13'] ?>.jpg"></td>
							<td>
								<h3>Book Details</h3>
								For course: <?php echo $book['courseID'] . " - " . $book['courseTitle'] . " (" . $book['credit'] . ")" ?><br/>
								Book Title:	<?php echo $book['bookTitle'] ?><br/>
								Price: $<?php echo $book['price'] ?><br/>
								Author(s):
									<?php $i = 0; ?>
									<?php foreach ($author as $auth) { ?>
										<?php echo $auth['firstName'] . " " . $auth['lastName'];
										if ($i != count($author) - 1) {
											echo ", ";
											$i++;
										} ?>
									<?php } ?><br/>

								Publisher: <?php echo $book['publisher'] ?><br/>
								Edition: <?php echo $book['edition'] . " (" . $book['publishDate'] . ")" ?><br/>
								Length: <?php echo $book['length'] . " pages" ?><br/>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="panel-body">
				<h4>Product Description:</h4>
				<?php echo $book['description'] ?>
			</div>
		</div>
	</div>

	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>
