<?php

require_once "auth.php";
require_once "../config/db.php";


/* CHECK PET ID */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: pets.php");
    exit();

}

$pet_id = (int) $_GET["id"];


/* CHECK IF PET EXISTS */

$stmt = $conn->prepare(
    "SELECT id
     FROM pets
     WHERE id = ?"
);

$stmt->bind_param("i", $pet_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: pets.php?error=not_found");
    exit();

}

$stmt->close();


/*
   DELETE CART ITEMS FIRST

   This prevents the foreign-key constraint
   from blocking deletion of the pet.
*/

$stmt = $conn->prepare(
    "DELETE FROM cart_items
     WHERE pet_id = ?"
);

$stmt->bind_param("i", $pet_id);
$stmt->execute();

$stmt->close();


/* DELETE PET */

$stmt = $conn->prepare(
    "DELETE FROM pets
     WHERE id = ?"
);

$stmt->bind_param("i", $pet_id);


if ($stmt->execute()) {

    $stmt->close();

    header("Location: pets.php?success=deleted");
    exit();

}


/* DELETE FAILED */

$stmt->close();

header("Location: pets.php?error=delete_failed");
exit();

?>
