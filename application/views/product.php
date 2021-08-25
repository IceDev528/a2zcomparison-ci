<?php $this->load->view('template/header'); ?>

<div role="main" class="main">
	<div class="col-md-10">
		<div class="product-overview shop">
			<div class="product-info">
				<div class="product-image-wrapper">
					<img class="fit-size" src="<?php echo $product->image_url; ?>" />
				</div>

				<div class="product-name">
					<h2><?php echo $product->brand; ?></h2>
					<div class="star-rating-wrapper">
						<span class="title">Overall Rating:&nbsp;&nbsp;</span>
						<div class="star-rating">
							<span style="width:<?php echo $product->overallrating*20; ?>%"></span>
						</div>
						<span class="marks"><?php echo $product->overallrating; ?></span>
					</div>
				</div>
			</div>

			<div class="product-overview-text">
				<h2>Product Overview</h2>
				<p><?php echo $product->description; ?></p>
			</div>

			<div class="compare-price-wrapper">
				<h2>Compare Prices:</h2>
				
				<table class="table mt-xl">
					<tbody>

						<?php 
						if(!empty($price_comparison_list)):

							foreach($price_comparison_list as $item): ?>

						<tr>
							<th class="site-name">
								<a href="<?php echo $item->url; ?>"><?php echo $item->merchant; ?></a>
							</th>
							<td class="shopping-type">
								<span><?php echo $item->shipType; ?></span>
							</td>
							<td class="price">
								<p class="price"><?php echo $item->price; ?><p>
									<?php 
									if(!empty($item->tax_price)):
										?>
									<p class="tax">+<?php echo $item->tax_price; ?> tax</p>
									<?php 
									endif;
									if(!empty($item->dropped_percent)):
										?>
									<p class="news"><i class="fa fa-arrow-circle-o-down"></i>&nbsp;Price dropped <?php echo $item->dropped_percent; ?>%</p>
									<?php 
									endif; 
									?>
								</td>
							</tr>

						<?php endforeach;

						endif;
						?>

					</tbody>
				</table>
			</div>
			
			<div class="top-reviews-wrapper">

				<?php if(!empty($review_list)): ?>

					<h2>Top Reviews:</h2>

					<?php foreach($review_list as $item): ?>

						<div class="review">
							<div class="star-rating-wrapper">
								<div title="Rated 5 out of 5" class="star-rating">
									<span style="width:<?php echo $item->rating*20; ?>%"></span>
								</div>
								<span class="marks"><?php echo $item->rating; ?></span>
							</div>
							
							<h2><?php echo $item->title; ?></h2>

							<?php
							$date = new DateTime($item->submissionTime);
							$date_string = $date->format('M d, Y');
							?>

							<span class="date"><?php echo $date_string; ?></span>

							<p><?php echo $item->comment; ?></p>

							<?php if(!empty($item->rating >= 3.5)): ?>
								<span class="recommend"><i class="fa fa-check-circle"></i>&nbsp;<span>I would recommend this to a friend!</span></span>
							<?php endif; ?>
						</div>

					<?php endforeach; ?>

				<?php else: ?>
					<h2>No Review</h2>
				<?php endif;?>
			</div>
		</div>
	</div>
	
	<div class="col-md-2 advertisement">
		<a class="pb-lg" href="#">
			<img src="<?php echo asset_url(); ?>img/advertisement/advertise-1.jpg" />
		</a>
		<a class="pt-lg" href="#">
			<img src="<?php echo asset_url(); ?>img/advertisement/advertise-2.jpg" />
		</a>
	</div>	

</div>

<?php $this->load->view('template/footer'); ?>