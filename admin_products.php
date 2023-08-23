<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['add_product'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $amount = $_POST['amount'];
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_product_name) > 0){
      $message[] = 'product name already added';
   }else{
      $add_product_query = mysqli_query($conn, "INSERT INTO `products`(name, amount, image) VALUES('$name', '$amount', '$image')") or die('query failed');

      if($add_product_query){
         if($image_size > 2000000){
            $message[] = 'image size is too large';
         }else{
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'product added successfully!';
         }
      }else{
         $message[] = 'product could not be added!';
      }
   }
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_query($conn, "SELECT image FROM `products` WHERE id = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_products.php');
}

if(isset($_POST['update_product'])){

   $update_p_id = $_POST['update_p_id'];
   $update_name = $_POST['update_name'];
   $update_amount = $_POST['update_amount'];

   mysqli_query($conn, "UPDATE `products` SET name = '$update_name', amount = '$update_amount' WHERE id = '$update_p_id'") or die('query failed');

   $update_image = $_FILES['update_image']['name'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_folder = 'uploaded_img/'.$update_image;
   $update_old_image = $_POST['update_old_image'];

   if(!empty($update_image)){
      if($update_image_size > 2000000){
         $message[] = 'image file size is too large';
      }else{
         mysqli_query($conn, "UPDATE `products` SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
         move_uploaded_file($update_image_tmp_name, $update_folder);
         unlink('uploaded_img/'.$update_old_image);
      }
   }

   header('location:admin_products.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->\
   <style>
      @import url('https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600&display=swap');

:root{
   --purple:#8e44ad;
   --red:#c0392b;
   --orange:#f39c12;
   --black:#333;
   --white:#fff;
   --light-color:#666;
   --light-white:#ccc;
   --light-bg:#f5f5f5;
   --border:.1rem solid var(--black);
   --box-shadow:0 .5rem 1rem rgba(0,0,0,.1);
}

*{
   font-family: 'Rubik', sans-serif;
   margin:0; padding:0;
   box-sizing: border-box;
   outline: none; border:none;
   text-decoration: none;
   transition:all .2s linear;
}

*::selection{
   background-color: var(--purple);
   color:var(--white);
}

*::-webkit-scrollbar{
   height: .5rem;
   width: 1rem;
}

*::-webkit-scrollbar-track{
   background-color: transparent;
}

*::-webkit-scrollbar-thumb{
   background-color: var(--purple);
}

html{
   font-size: 62.5%;
   overflow-x: hidden;
}

body{
   background-color: var(--light-bg);
}

section{
   padding:3rem 2rem;
}

.title{
   text-align: center;
   margin-bottom: 2rem;
   text-transform: uppercase;
   color:var(--black);
   font-size: 4rem;
}

.empty{
   padding:1.5rem;
   text-align: center;
   border:var(--border);
   background-color: var(--white);
   color:var(--red);
   font-size: 2rem;
}

.message{
   position: sticky;
   top:0;
   margin:0 auto;
   max-width: 1200px;
   background-color: var(--light-bg);
   padding:2rem;
   display: flex;
   align-items: center;
   justify-content: space-between;
   z-index: 10000;
   gap:1.5rem;
}

.message span{
   font-size: 2rem;
   color:var(--black);
}

.message i{
   cursor: pointer;
   color:var(--red);
   font-size: 2.5rem;
}

.message i:hover{
   transform: rotate(90deg);
}

.btn,
.option-btn,
.delete-btn,
.white-btn{
   display: inline-block;
   margin-top: 1rem;
   padding:1rem 3rem;
   cursor: pointer;
   color:var(--white);
   font-size: 1.8rem;
   border-radius: .5rem;
   text-transform: capitalize;
}

.btn:hover,
.option-btn:hover,
.delete-btn:hover{
   background-color: var(--black);
}

.white-btn,
.btn{
   background-color: var(--purple);
}

.option-btn{
   background-color: var(--orange);
}

.delete-btn{
   background-color: var(--red);
}

.white-btn:hover{
   background-color: var(--white);
   color:var(--black);
}

@keyframes fadeIn {
   0%{
      transform: translateY(1rem);
      opacity: .2s;
   }
}

.header{
   position: sticky;
   top:0; left:0; right:0;
   z-index: 1000;
   background-color: var(--white);
   box-shadow: var(--box-shadow);
}

.header .flex{
   display: flex;
   align-items: center;
   padding:2rem;
   justify-content: space-between;
   position: relative;
   max-width: 1200px;
   margin:0 auto;
}
 
.header .flex .logo{
   font-size: 2.5rem;
   color:var(--black);
}

.header .flex .logo span{
   color:var(--purple);
}

.header .flex .navbar a{
   margin:0 1rem;
   font-size: 2rem;
   color:var(--black);
}

.header .flex .navbar a:hover{
   color:var(--purple);
}

.header .flex .icons div{
   margin-left: 1.5rem;
   font-size: 2.5rem;
   cursor: pointer;
   color:var(--black);
}

.header .flex .icons div:hover{
   color:var(--purple);
}

.header .flex .account-box{
   position: absolute;
   top:120%; right:2rem;
   width: 30rem;
   box-shadow: var(--box-shadow);
   border-radius: .5rem;
   padding:2rem;
   text-align: center;
   border-radius: .5rem;
   border:var(--border);
   background-color: var(--white);
   display: none;
   animation:fadeIn .2s linear;
}

.header .flex .account-box.active{
   display: inline-block;
}

.header .flex .account-box p{
   font-size: 2rem;
   color:var(--light-color);
   margin-bottom: 1.5rem;
}

.header .flex .account-box p span{
   color:var(--purple);
}

.header .flex .account-box .delete-btn{
   margin-top: 0;
}

.header .flex .account-box div{
   margin-top: 1.5rem;
   font-size: 2rem;
   color:var(--light-color);
}

.header .flex .account-box div a{
   color:var(--orange);
}

.header .flex .account-box div a:hover{
   text-decoration: underline;
}

#menu-btn{
   display: none;
}

.dashboard .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(25rem, 1fr));
   gap:1.5rem;
   max-width: 1200px;
   margin:0 auto;
   align-items: flex-start;
}

.dashboard .box-container .box{
   border-radius: .5rem;
   padding:2rem;
   background-color: var(--white);
   box-shadow: var(--box-shadow);
   border:var(--border);
   text-align: center;
}

.dashboard .box-container .box h3{
   font-size: 5rem;
   color:var(--black); 
}

.dashboard .box-container .box p{
   margin-top: 1.5rem;
   padding:1.5rem;
   background-color: var(--light-bg);
   color:var(--purple);
   font-size: 2rem;
   border-radius: .5rem;
   border:var(--border);
}

.add-products form{
   background-color: var(--white);
   border-radius: .5rem;
   padding:2rem;
   text-align: center;
   box-shadow: var(--box-shadow);
   border:var(--border);
   max-width: 50rem;
   margin:0 auto;
}

.add-products form h3{
   font-size: 2.5rem;
   text-transform: uppercase;
   color:var(--black);
   margin-bottom: 1.5rem;
}

.add-products form .box{
   width: 100%;
   background-color: var(--light-bg);
   border-radius: .5rem;
   margin:1rem 0;
   padding:1.2rem 1.4rem;
   color:var(--black);
   font-size: 1.8rem;
   border:var(--border);
}

.show-products .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 30rem);
   justify-content: center;
   gap:1.5rem;
   max-width: 1200px;
   margin:0 auto;
   align-items: flex-start;
}

.show-products{
   padding-top: 0;
}

.show-products .box-container .box{
   text-align: center;
   padding:2rem;
   border-radius: .5rem;
   border:var(--border);
   box-shadow: var(--box-shadow);
   background-color: var(--white);
}

.show-products .box-container .box img{
   height: 20rem;
}

.show-products .box-container .box .name{
   padding:1rem 0;
   font-size: 2rem;
   color:var(--black);
}

.show-products .box-container .box .price{
   padding:1rem 0;
   font-size: 2.5rem;
   color:var(--red);
}

.edit-product-form{
   min-height: 100vh;
   background-color: rgba(0,0,0,.7);
   display: flex;
   align-items: center;
   justify-content: center;
   padding:2rem;
   overflow-y: scroll;
   position: fixed;
   top:0; left:0; 
   z-index: 1200;
   width: 100%;
}

.edit-product-form form{
   width: 50rem;
   padding:2rem;
   text-align: center;
   border-radius: .5rem;
   background-color: var(--white);
}

.edit-product-form form img{
   height: 25rem;
   margin-bottom: 1rem;
}

.edit-product-form form .box{
   margin:1rem 0;
   padding:1.2rem 1.4rem;
   border:var(--border);
   border-radius: .5rem;
   background-color: var(--light-bg);
   font-size: 1.8rem;
   color:var(--black);
   width: 100%;
}

.orders .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 30rem);
   justify-content: center;
   gap:1.5rem;
   max-width: 1200px;
   margin:0 auto;
   align-items: flex-start;
}

.orders .box-container .box{
   background-color: var(--white);
   padding:2rem;
   border:var(--border);
   box-shadow: var(--box-shadow);
   border-radius: .5rem;
}

.orders .box-container .box p{
   padding-bottom: 1rem;
   font-size: 2rem;
   color:var(--light-color);
}

.orders .box-container .box p span{
   color:var(--purple);
}

.orders .box-container .box form{
   text-align: center;
}

.orders .box-container .box form select{
   border-radius: .5rem;
   margin:.5rem 0;
   width: 100%;
   background-color: var(--light-bg);
   border:var(--border);
   padding:1.2rem 1.4rem;
   font-size: 1.8rem;
   color:var(--black);
}

.users .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 30rem);
   justify-content: center;
   gap:1.5rem;
   max-width: 1200px;
   margin:0 auto;
   align-items: flex-start;
}

.users .box-container .box{
   background-color: var(--white);
   padding:2rem;
   border:var(--border);
   box-shadow: var(--box-shadow);
   border-radius: .5rem;
   text-align: center;
}

.users .box-container .box p{
   padding-bottom: 1.5rem;
   font-size: 2rem;
   color:var(--light-color);
}

.users .box-container .box p span{
   color:var(--purple);
}

.users .box-container .box .delete-btn{
   margin-top: 0;
}

.messages .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 35rem);
   justify-content: center;
   gap:1.5rem;
   max-width: 1200px;
   margin:0 auto;
   align-items: flex-start;
}

.messages .box-container .box{
   background-color: var(--white);
   padding:2rem;
   border:var(--border);
   box-shadow: var(--box-shadow);
   border-radius: .5rem;
}

.messages .box-container .box p{
   padding-bottom: 1.5rem;
   font-size: 2rem;
   color:var(--light-color);
   line-height: 1.5;
}

.messages .box-container .box p span{
   color:var(--purple);
}

.messages .box-container .box .delete-btn{
   margin-top: 0;
}




















/* media queries  */

@media (max-width:991px){

   html{
      font-size: 55%;
   }

}

@media (max-width:768px){

   #menu-btn{
      display: inline-block;
   }

   .header .flex .navbar{
      position: absolute;
      top:99%; left:0; right:0;
      background-color: var(--white);
      border-top: var(--border);
      border-bottom: var(--border);
      clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
   }

   .header .flex .navbar.active{
      clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
   }

   .header .flex .navbar a{
      margin:2rem;
      display: block;
   }

}

@media (max-width:450px){

   html{
      font-size: 50%;
   }

   .title{
      font-size: 3rem;
   }

}
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- product CRUD section starts  -->

<section class="add-products">

   <h1 class="title">shop products</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <h3>add product</h3>
      <select name="product_type" class="box">
         <option value="pc">Pc</option>
         <option value="mobile">Mobile</option>
      <input type="text" name="name" class="box" placeholder="enter product name" required>
      <input type="number" min="0" name="amount" class="box" placeholder="enter product amount" required>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
      <input type="submit" value="add product" name="add_product" class="btn">
   </form>

</section>

<!-- product CRUD section ends -->

<!-- show products  -->

<section class="show-products">

   <div class="box-container">

      <?php
         $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
      <div class="box">
         <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
         <div class="type"><?php echo $fetch_products['product_type']; ?></div>
         <div class="name"><?php echo $fetch_products['name']; ?></div>
         <div class="amount"><?php echo $fetch_products['amount']; ?></div>
         <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">update</a>
         <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
      ?>
   </div>

</section>

<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
      <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
      <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
      <select name="product_type" class="box" id="type">
         <option value="pc">Pc</option>
         <option value="mobile">Mobile</option>
      <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="enter product name">
      <input type="number" name="update_amount" value="<?php echo $fetch_update['amount']; ?>" min="0" class="box" required placeholder="enter product price">
      <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
      <input type="submit" value="update" name="update_product" class="btn">
      <input type="reset" value="cancel" id="close-update" class="option-btn">
   </form>
   <?php
         }
      }
      }else{
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
   ?>

</section>







<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>