<?php $this->load->view('template/header'); ?>

<div role="main" class="main">
	<div class="home-search" id="home-search">
		<div class="wrapper">
			<div class="header-search">
				<form id="searchForm" action="<?php echo base_url().'index'.'/'.$category; ?>" method="get">
					<div class="input-group">
						<input type="text" class="form-control" name="q" id="q" value="<?php echo $keyword; ?>" placeholder="What Product Are You Looking For" required>
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
						</span>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<section class="a2z-notice">
		<div class="wrapper">
			<div class="col-md-12">
				<div class="notice-text">
					<p>A2Z Reviews aggregates product reviews from the top sources to provide a one stop source for shoppers. Our site has been designed to show you the ratings, reviews and compare prices so you find the best deal!</p>
				</div>
			</div>
			<div class="col-md-12">
				<div class="notice-user-text">
					<p>“I love A2Z Reviews - so easy to use plus you don’t need to be a member to access the information.”</p>
				</div>
			</div>
		</div>
	</section>

	<div class="">
		<div class="categories-container products-container">
			<h2 class="title mt-lg">Products</h2>

			<div class="row">
				<?php 
				if ($totalRecords>0):
					foreach($product_list as $product): ?>

				<div class="category-item col-lg-3 col-md-4 col-xs-6">
					<a href="<?php echo base_url().'product'.'/'.$product->catalogId; ?>">
						<img class="icon-category" src="<?php echo $product->image_url; ?>" />
						<h2 class="header-category"><?php echo $product->brand; ?></h2>
					</a>	
				</div>

				<?php 
				endforeach; 
				else:
					?>

				<h2 class="text-center">There are no result</h2>
				<?php 
				endif;
				?>
			</div>
		</div>
	</div>	
</div>

<?php $this->load->view('template/footer'); ?>