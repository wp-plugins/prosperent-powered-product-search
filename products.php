<?php
function pagination($pages = '', $range)
{
     $showitems = ($range * 2)+1;

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }

     if(1 != $pages)
     {
         echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";


         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }

         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}
?>

<script type="text/javascript">
<!--
function toggle_visibility(id)
{
       var e = document.getElementById(id);
       if(e.style.display == 'none')
           e.style.display = 'block';
}

function toggle_hidden(id)
{
       var e = document.getElementById(id);
       if(e.style.display == 'block')
           e.style.display = 'none';
}
//-->
</script>

<?php
$sort = !$_GET['sortBy'] ? (!get_option('Default_Sort') ? 'relevance desc' : get_option('Default_Sort')) : $_GET['sortBy'];
$filterMerchant = $_GET['filterMerchant'];
$filterBrand = $_GET['filterBrand'];
$q = ('' != $_GET['q']) ? $_GET['q'] : get_option('Starting_Query');
$query = stripslashes($q);

$minusBrands = explode(',', get_option('Negative_Brand'));

$negativeBrands = array();
foreach ($minusBrands as $negative)
{
    $negativeBrands[] = '!' . trim($negative);
}

array_unshift($negativeBrands, $filterBrand);

$minusMerchants = explode(',', get_option('Negative_Merchant'));

$negativeMerchants = array();
foreach ($minusMerchants as $negative)
{
    $negativeMerchants[] = '!' . trim($negative);
}

array_unshift($negativeMerchants, $filterMerchant);

/*
/  Prosperent API Query
*/

require_once('Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
    'api_key'        => get_option('Api_Key'),
    'query'          => $query,
    'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
    'page'           => 1,
    'limit'          => !get_option('Api_Limit') ? 100 : get_option('Api_Limit'),
    'sortBy'	       => $sort,
    'groupBy'	       => 'productId',
    'enableFacets'   => !get_option('Enable_Facets') ? TRUE : get_option('Enable_Facets'),
    'filterBrand'    => !get_option('Negative_Brand') ? $filterBrand : $negativeBrands,
    'filterMerchant' => !get_option('Negative_Merchant') ? $filterMerchant : $negativeMerchants
));

/*
/  Fetching results and pulling back all data
/  To see which data is available to pull back login in to
/  Prosperent.com and click the API tab
*/
$prosperentApi -> fetch();
$results = $prosperentApi -> getAllData();

$totalFound = $prosperentApi -> getTotalRecordsFound();
$facets = $prosperentApi -> getFacets();

/*
/  If no results, or the user clicked search when 'Search Products...'
/  was in the search field, displays 'No Results'
*/
if ($query == 'Search Products...' || empty($results) || $query == 'No Query')
{
    echo '<div class="noResults">No Results</div>' . '</br>';

    echo '<div class="noResults-secondary">Please refine your search.</div>';
    $noResults = TRUE;
}

if (!$noResults)
{
    if ($prosperentApi->get_enableFacets() == 1)
    {
        $brands = $facets['brand'];
        $merchants = $facets['merchant'];

        if (!empty($brands))
        {
            $brands1 = array_slice($brands, 0, !get_option('Brand_Facets') ? 10 : get_option('Brand_Facets'), true);
            $brands2 = array_slice($brands, !get_option('Brand_Facets') ? 10 : get_option('Brand_Facets'), 100);

            $brandNames = array();
            foreach ($brands2 as $brand)
            {
                $brandNames[] = ucfirst($brand[value]);
            }

            array_multisort($brandNames, SORT_REGULAR, $brands2);
        }

        if (!empty($merchants))
        {
            $merchants1 = array_slice($merchants, 0, !get_option('Merchant_Facets') ? 12 : get_option('Merchant_Facets'), true);
            $merchants2 = array_slice($merchants, !get_option('Merchant_Facets') ? 12 : get_option('Merchant_Facets'), 100);

            $merchantNames = array();
            foreach ($merchants2 as $merchant)
            {
                $merchantNames[] = ucfirst($merchant[value]);
            }

            array_multisort($merchantNames, SORT_STRING, $merchants2);
        }

        ?>

        <table id="facets">
            <tr>
                <td class="brands">
                    <?php
                    echo (empty($filterBrand) ? '<div class="browseBrands">Browse by Brand: </div>' : '<div class="filteredBrand">Filtered by Brand: </div>');
                    if (empty($facets['brand']))
                    {
                        echo '<div class="noBrands">No Brands Found</div>';
                    }
                    else if (!$filterBrand)
                    {
                        foreach ($brands1 as $i => $brand)
                        {
                            if ($i < count($brands1) - 1)
                            {
                                echo '<a href=http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&filterBrand=' . urlencode($brand['value']) . '>' . $brand['value'] . ' (' . $brand['count'] . ')</a>, ';
                }
                            else
                            {
                                echo '<a href=http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&filterBrand=' . urlencode($brand['value']) . '>' . $brand['value'] . ' (' . $brand['count'] . ')</a>';
                }
                        }
                        if (!empty($brands2))
                        {
                            ?>
                            </br>
                               <a onclick="toggle_visibility('brandList'); toggle_hidden('merchantList'); toggle_hidden('moreBrands'); toggle_visibility('hideBrands'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreBrands" style="display:block;">More Brands <img src="<?php echo plugins_url('/img/arrow_down_small.png', __FILE__); ?>"/></span></a>
                            <a onclick="toggle_hidden('brandList'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="hideBrands" style="display:none;">Hide Brands <img src="<?php echo plugins_url('/img/arrow_up_small.png', __FILE__); ?>" /></span></a>
                            <?php
                        }
                    }
                    else
                    {
                        echo $filterBrand;
                        echo '</br><a href=http://' . $_SERVER['HTTP_HOST'] . str_replace('&filterBrand=' . urlencode($filterBrand), '', $_SERVER['REQUEST_URI']) . '>clear filter</a>';
                    }
                    ?>
                </td>
                <td class="merchants">
                    <?php
                    echo (empty($filterMerchant) ? '<div class="browseMerchants">Browse by Merchant: </div>' : '<div class="filteredMerchants">Filtered by Merchant: </div>');

                    if (empty($facets['merchant']))
                    {
                        echo '<div class="noMerchants"">No Merchants Found</div>';
                    }
                    else if (!$filterMerchant)
                    {
                        foreach ($merchants1 as $i => $merchant)
                        {
                            if ($i < count($merchants1) - 1)
                            {
                                echo '<a href=http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&filterMerchant=' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a>, ';
                            }

                            else
                            {
                                echo '<a href=http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&filterMerchant=' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a>';
                            }
                        }
                        if (!empty($merchants2))
                        {
                            ?>
                            </br>
                            <a onclick="toggle_visibility('merchantList'); toggle_hidden('brandList'); toggle_hidden('moreMerchants'); toggle_visibility('hideMerchants'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreMerchants" style="display:block;">More Merchants <img src="<?php echo plugins_url('/img/arrow_down_small.png', __FILE__); ?>"/></span></a>
                            <a onclick="toggle_hidden('merchantList'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); " style="cursor:pointer; font-size:12px;"><span id="hideMerchants" style="display:none;">Hide Merchants <img src="<?php echo plugins_url('/img/arrow_up_small.png', __FILE__); ?>" /></span></a>
                            <?php
                        }
                    }
                    else
                    {
                        echo $filterMerchant;
                        echo '</br><a href=http://' . $_SERVER['HTTP_HOST'] . str_replace('&filterMerchant=' . urlencode($filterMerchant), '', $_SERVER['REQUEST_URI']) . '>clear filter</a>';
                    }
                    ?>
                </td>
            </tr>
        </table>
        <table id="brandList" style="display:none; font-size:11px; width:100%; background:#F0F4F5; table-layout:fixed;">
            <?php
            echo '<th style="padding:3px 0 0 5px; font-size:13px;">More Brands: </th>';

            foreach ($brands2 as  $i => $brand)
            {
                if ($i == 0 || $i % 5 == 0 && $i >= 5)
                {
                    echo '<tr>';
                }

                echo '<td style="width:1%; padding:5px; height:30px;"><a href=http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&filterBrand=' . urlencode($brand['value']) . '>' . $brand['value'] . ' (' . $brand['count'] . ')</a></td>';

                if ($i % 5 == 4 && $i >= 9)
                {
                    echo '</tr>';
                }

                $i++;
            }
            ?>
        </table>
        <table id="merchantList" style="display:none; font-size:11px; background:#F0F4F5; width:100%;">
            <?php
            echo '<th style="padding:3px 0 0 5px; font-size:13px;">More Merchants: </th>';

            foreach ($merchants2 as $i => $merchant)
            {
                if ($i == 0 || $i % 4 == 0 && $i >= 4)
                {
                    echo '<tr>';
                }

                echo '<td style="padding:5px; height:30px; width:1%;"><a href=http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&filterMerchant=' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a></td>';

                if ($i % 4 == 3 && $i >= 7)
                {
                    echo '</tr>';
                }

                $i++;
            }
            ?>
        </table>

        <div class="table-seperator"></div>

        <?php
    }

    echo '<div class="totalFound">' . $totalFound . ' results for <b>' . strtolower($query) . '</b></div>';
    ?>

     <form name="priceSorter" method="GET" action="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" style="float:right; padding-right:13px; padding-bottom:10px;">
        <input type="hidden" name="q" value="<?php echo $query;?>">
        <input type="hidden" name="filterBrand" value="<?php echo $filterBrand;?>">
        <input type="hidden" name="filterMerchant" value="<?php echo $filterMerchant;?>">
        <label for="PriceSort" style="font-color:#cc6600; font-size:14px;">Sort By: </label>
        <select name="sortBy" onChange="priceSorter.submit();">
            <option> -- Select Option -- </option>
            <option value="relevance desc">Relevancy</option>
            <option value="price desc">Price: High to Low</option>
            <option value="price asc">Price: Low to High</option>
        </select>
    </form>
    </br>

    <?php
    // Gets the count of results for Pagination
    $productCount = count($results);

    // Pagination limit, can be changed
    $limit = !get_option('Pagination_Limit') ? 15 : get_option('Pagination_Limit');

    $pages = round($productCount / $limit, 0);

    if ($pageNumber  < 1)
    {
        $pageNumber  = 1;
    }
    else if ($pageNumber  > ceil(($productCount + 1) / $limit))
    {
        $pageNumber  = ceil(($productCount + 1) / $limit);
    }

    $limitLower = ($pageNumber  - 1) * $limit;

    // Breaks the array into smaller chunks for each page depending on $limit
    $results = array_slice($results, $limitLower, $limit, true);

    ?>

    <table id="productList">
        <?php
        // Loop to return Products and corresponding information
        foreach ($results as $i => $record)
        {
            $record['image_url'] = preg_replace('/\/images\/250x250\//', '/images/125x125/', $record['image_url'])
            ?>
                <tr class="productBlock">
                    <td class="productImage">
                        <a href="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" onclick="javascript:document.location='<?php echo $record['affiliate_url']?>';return false;"><span><img src="<?php echo $record['image_url']?>"  alt="<?php echo $record['keyword']?>" title="<?php echo $record['keyword']?>"></span></a>
                    </td>
                    </td>
                    <td class="productContent">
                        <div class="productTitle"><a href="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" onclick="javascript:document.location='<?php echo $record['affiliate_url']; ?>';return false;"><span><?php echo $record['keyword']?></span></a></div>
                        <div class="productDescription"><?php echo substr($record['description'], 0, 275) . '...'; ?></div>
                        <div class="productBrandMerchant">
                            <?php
                            if($record['brand'])
                            {
                                echo '<u>Brand</u>: <a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&filterBrand=' . urlencode($record['brand']). '"><cite>' . $record['brand'] . '</cite></a>&nbsp&nbsp';
                            }
                            if($record['merchant'])
                            {
                                echo '<u>Merchant</u>: <a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&filterMerchant=' . urlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a>';
                            }
                            ?>
                        </div>
                    </td>
                    <td class="productEnd">
                        <?php
                        if(empty($record['price_sale']) || $record['price'] <= $record['price_sale'])
                        {
                            //we don't do anything
                            ?>
                            <div class="productPriceNoSale"><span><?php echo '$' . $record['price']?></span></div>
                            <?php
                        }
                        //otherwise strike-through Price and list the Price_Sale
                        else
                        {
                            ?>
                            <div class="productPrice"><span>$<?php echo $record['price']?></span></div>
                            <div class="productPriceSale"style="padding-bottom:15px;"><span>$<span><?php echo $record['price_sale']?></span></span></div>
                            <?php
                        }
                        ?>
                        <a href="<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" onclick="javascript:document.location='<?php echo $record['affiliate_url']; ?>';return false;"><img src="<?php echo plugins_url('/img/visit_store_button.gif', __FILE__);?> "></a>
                    </td>
                </tr>
            <?php
        }
        ?>
    </table>
    <?php
    pagination($pages, $pages);
}
