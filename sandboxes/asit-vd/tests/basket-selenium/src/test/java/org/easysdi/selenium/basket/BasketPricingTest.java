package org.easysdi.selenium.basket;

import com.google.common.base.Function;
import java.io.File;
import java.io.IOException;
import java.sql.Driver;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.TimeUnit;
import java.util.logging.Level;
import java.util.logging.Logger;
import org.apache.commons.io.FileUtils;
import org.openqa.selenium.support.PageFactory;
import org.testng.Assert;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;

import org.easysdi.selenium.basket.pages.HomePage;
import org.easysdi.selenium.basket.pages.SheetPage;
import org.easysdi.selenium.basket.pages.BasketPage;
import org.easysdi.selenium.util.Consts;
import org.easysdi.selenium.util.SdiBasket;
import org.easysdi.selenium.util.SdiMetadata;
import org.easysdi.selenium.util.SdiOrganism;
import org.easysdi.selenium.util.SdiUser;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.OutputType;
import org.openqa.selenium.TakesScreenshot;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.openqa.selenium.support.ui.Select;
import org.testng.annotations.AfterClass;

public class BasketPricingTest extends TestNgTestBase {

    /* CONFIG */
    //TODO: change string with config
    private final String noTotalMessage = "-";

    /* ***** platform config: */
    /**
     * Is the pricing activated ? YES<br/>
     * VAT : 8<br/>
     * Currency : CHF <br/>
     * Decimal separator : .<br/>
     * Number of digits after decimal : 2<br/>
     * Thousants separator : '<br/>
     * Rounding rule : 0.05<br/>
     * Overall fee : 50<br/>
     * Is the overall fee perceived if the data are free? No<br/>
     */
    /**
     * Categories with special fees: - Membre ASIT VD : fee 5.00
     *
     */
    /* ***** Remarks */
    /**
     * - GDB means "geodonnées de base", all GDB profiles have free categories
     * for communes and canton<br/>
     */

    /* ******* Metadatas (products), organisms and clients */
    /* *** Commune ***
     * Pricing:         Free for internal          Fixed costs             Apply fixed costs if data free
     *                  YES                        0.00                    NO
     *    Rebates :
     *     - Membre ASIT VD : 15.00%
     *
     * TarifProfiles :  Fixed price  Price by km2  Min. price  Max. price  Free category ?
     * - GDB-com-15:    0.00         15.00         0.00        0.00        "Commune"
     *                                                                     "Administration Cantonale Vaudoise"
     * */
    private final SdiOrganism orgCommune = new SdiOrganism("uneCommune", 4);
    private final SdiUser cliCommune = new SdiUser("commune", "cliCommune", "987654321");

    /* Fee */
    private final SdiMetadata mdProdCommuneFee = new SdiMetadata("prodCommuneFee", "9");
    /* Has profile GDB-com-15 */
    private final SdiMetadata mdProdCommuneProfileGDB = new SdiMetadata("prodCommuneProfileGDB", "11");
    /* Free */
    private final SdiMetadata mdProdCommuneFree = new SdiMetadata("prodCommuneFree", "10");

    /* *** Canton ***
     * Pricing:         Free for internal          Fixed costs             Apply fixed costs if data free
     *                  YES                        25.00                   NO
     *    Rebates :
     *     - Membre ASIT VD : 15.00%
     *     - Ecole          : 100.00%
     * 
     * TarifProfiles :  Fixed price  Price by km2  Min. price  Max. price  Free category ?
     * - GDB-can-10 :   0.00         10.00         0.00        0.00        "Commune"
     *                                                                     "Administration Cantonale Vaudoise"
     * - can-10 :       0.00         10.00         0.00        0.00        -
     */
    private final SdiOrganism orgCanton = new SdiOrganism("lEtatDeVaud", 3);
    private final SdiUser cliCanton = new SdiUser("canton", "cliCanton", "987654321");
    /* Free */
    private final SdiMetadata mdProdCantonFree = new SdiMetadata("prodCantonFree", "6");
    /* Has profile GDB-can-10 */
    private final SdiMetadata mdProdCantonProfileGDB = new SdiMetadata("prodCantonProfileGDB", "5");
    /* Has profile can-10 */
    private final SdiMetadata mdProdCantonProfile = new SdiMetadata("prodCantonProfile", "8");
    private final SdiMetadata mdProdCantonProfile2 = new SdiMetadata("ProdCantonProfile2", "12");
    /* Fee */
    private final SdiMetadata mdProdCantonFee = new SdiMetadata("prodCantonFee", "7");

    private final SdiUser cliEcole = new SdiUser("commune", "cliEcole", "987654321");
    private final SdiUser cliEtudiant = new SdiUser("commune", "cliEtudiant", "987654321");
    private final SdiUser cliNonMembre = new SdiUser("commune", "cliNonMembre", "987654321");
    private final SdiOrganism orgMembre = new SdiOrganism("bureauMembre", 8);
    private final SdiUser cliMembre = new SdiUser("commune", "cliMembre", "987654321");
    private final SdiUser cliReseaux = new SdiUser("reseaux", "cliReseaux", "987654321");
    private final SdiUser cliSansCategorie = new SdiUser("sansCategorie", "cliSansCategorie", "987654321");
    private final SdiUser cliDoubleCat = new SdiUser("deuxCatégories", "cliDoubleCat", "987654321");

    /**
     * Run before tests in this class
     */
    @BeforeClass
    public void testInit() {
        //?
    }

    /**
     * Test a client without category, a product with with a profile.
     */
    @Test
    public void clientWithoutCateg1ProdWithProfile() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("clientWithoutCateg1ProdWithProfile", "1000000");
        basket.metadatas.add(mdProdCantonProfile);
        // 10.- for 1sqkm + 0.8.- VAT + 25.- Fixed processing fee +  50.- platform = 85.50.-
        Assert.assertEquals(getBasketTotalPrice(basket), "85.80 CHF");
    }

    /**
     * Test a client without category, a product with with a profile.
     */
    @Test
    public void clientWithoutCateg1ProdWithProfileRounding() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("clientWithoutCateg1ProdWithProfileRounding", "100000");
        basket.metadatas.add(mdProdCantonProfile);
        // 1.- for 1sqkm + 0.08.- VAT + 25.- Fixed processing fee +  50.- platform = 76.08.- rounded at 76.10
        Assert.assertEquals(getBasketTotalPrice(basket), "76.10 CHF");
    }

    /**
     * Test a client without category, a product with with a fee product.
     */
    @Test
    public void clientWithoutCateg1ProdWithFee() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("clientWithoutCateg1ProdWithFee", "1000000");
        basket.metadatas.add(mdProdCantonFee);
        // Should not see a total
        //TODO : check for "undefined price" string
        Assert.assertEquals(getBasketTotalPrice(basket), noTotalMessage);
    }

    /**
     * Test a client without category, a product with with a free product.
     */
    @Test
    public void clientWithoutCateg1ProdWithFree() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("clientWithoutCateg1ProdWithFree", "1000000");
        basket.metadatas.add(mdProdCantonFree);
        // All prods free -> 0.00
        Assert.assertEquals(getBasketTotalPrice(basket), "0 CHF");
    }

    /**
     * Test a client with 2 categories, a product with with a profile.
     */
    @Test
    public void client2Categ1ProdWithProfile() {
        login(cliDoubleCat);
        SdiBasket basket = new SdiBasket("client2Categ1ProdWithProfile", "1000000");
        basket.metadatas.add(mdProdCantonProfile);
        // This user is in an organism with 2 categories, the provider gives 10% & 20% rebates
        // The higher rebates should be used !!!!!!!!!!!!!
        // 10.- for 1sqkm *0.8 (rebate) => 8.- *1.08 (VAT) = 8.64, rounded to 8.65
        // + 25.- Fixed processing fee +  50.- platform = 83.64.- -> 83.65.-
        Assert.assertEquals(getBasketTotalPrice(basket), "85.80 CHF");
    }

    /**
     * Test a member client, a product with with a profile.
     */
    @Test
    public void member1ProdWithProfile() {
        login(cliMembre);
        SdiBasket basket = new SdiBasket("member1ProdWithProfile", "2000000");
        basket.metadatas.add(mdProdCantonProfile);
        // (20.- for 2sqkm * 0.85)=> 17.- + 1.36.- VAT + 25.- Fixed processing fee +  5.- platform (special category) = 48.36.- rounded at 48.35.-
        Assert.assertEquals(getBasketTotalPrice(basket), "48.35 CHF");
    }

    /**
     * Test a member client, a product with with a profile.
     */
    @Test
    public void member2ProdWithProfileCan() {
        login(cliMembre);
        SdiBasket basket = new SdiBasket("member2ProdWithProfileCan", "2000000");
        basket.metadatas.add(mdProdCantonProfile);
        basket.metadatas.add(mdProdCantonProfile2);
        //  (20.- for 2sqkm * 0.85)=> 17.- *1.08 VAT  // prod 1 // 18.36
        //+ (20.- for 2sqkm * 0.85)=> 17.- *1.08 VAT  // prod 2 // 18.36      
        //+ 25.- Fixed processing fee +  5.- platform (special category) = 66.72.- rounded at 66.70.-
        Assert.assertEquals(getBasketTotalPrice(basket), "66.70 CHF");
    }

    /**
     * Test a member client, a product with with a profile.
     */
    @Test
    public void member1ProdWithProfileCan1ProdWithProfileCom() {
        login(cliMembre);
        SdiBasket basket = new SdiBasket("member2ProdWithProfileCan", "2000000");
        basket.metadatas.add(mdProdCantonProfile);
        basket.metadatas.add(mdProdCommuneProfileGDB);
        //  (20.- for 2sqkm * 0.85)=> 17.- *1.08 VAT  // prod 1 // 18.36 rounded at 18.35
        //  + 25.- Fixed processing fee 
        //+ (30.- for 2sqkm * 0.85)=> 25.5.- *1.08 VAT  // prod 2 // 27.54 rounded at 27.55  
        //+  5.- platform (special category) = 75.90
        Assert.assertEquals(getBasketTotalPrice(basket), "75.90 CHF");
    }

    /**
     * Test a member client, a product with with a fee product.
     */
    @Test
    public void member1ProdWithFee() {
        login(cliMembre);
        SdiBasket basket = new SdiBasket("member1ProdWithFee", "2000000");
        basket.metadatas.add(mdProdCantonFee);
        // Should not see a total
        //TODO : check for "undefined price" string
        Assert.assertEquals(getBasketTotalPrice(basket), noTotalMessage);
    }

    /**
     * Test a member client, a product with with a free product.
     */
    @Test
    public void member1ProdWithFree() {
        login(cliMembre);
        SdiBasket basket = new SdiBasket("member1ProdWithFree", "2000000");
        basket.metadatas.add(mdProdCantonFree);
        // All prods free -> 0.00
        Assert.assertEquals(getBasketTotalPrice(basket), "0 CHF");
    }

    /**
     * Test a member client, a product with with a profile + a free product.
     */
    @Test
    public void member1ProdWithProfile1ProdFree() {
        login(cliMembre);
        SdiBasket basket = new SdiBasket("member1ProdWithProfile1ProdFree", "2000000");
        basket.metadatas.add(mdProdCantonProfile);
        // 1 free + (20.- for 2sqkm * 0.85)=> 17.- + 1.6.- VAT + 25.- Fixed processing fee +  5.- platform (special category) = 48.36.- rounded at 48.35.-
        Assert.assertEquals(getBasketTotalPrice(basket), "48.35 CHF");
    }
    
    
    /**
     * Test a non cetegirzed client client, a product with with a profile for a member third party.
     */
    @Test
    public void withoutcategOrder1ProdWithProfileForThirdParyMember() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("withoutcategOrder1ProdWithProfileForThirdParyMember", "2000000");
        basket.metadatas.add(mdProdCantonProfile);
        basket.setThridparty(orgMembre);
        // rebates comes from third party organism
        // 1 free + (20.- for 2sqkm * 0.85)=> 17.- + 1.6.- VAT + 25.- Fixed processing fee +  5.- platform (special category) = 48.36.- rounded at 48.35.-
        Assert.assertEquals(getBasketTotalPrice(basket), "48.35 CHF");
    }
    

    /**
     * Test a member client, a product with with a profile from Canton<br/>
     * and Canton as a third party -> same behavior as internal order.
     */
    @Test
    public void member1ProdWithProfileThirdPartyCanton() {
        login(cliMembre);
        SdiBasket basket = new SdiBasket("member1ProdWithProfile1ProdFree", "2000000");
        basket.metadatas.add(mdProdCantonProfile);
        basket.setThridparty(orgCanton);
        // With a third party "Canton", this product is internal, and shoul be free
        Assert.assertEquals(getBasketTotalPrice(basket), "0 CHF");
    }

    /**
     * Test an internal order with 1 product with tarification profile. <br/>
     * provider: canton<br/>
     * client: canton<br/>
     * total should be 0.00 because all products are internal
     */
    @Test
    public void internalOrder1ProdWithProfileCanton() {
        login(cliCanton);
        SdiBasket basket = new SdiBasket("internalOrder1ProdWithProfileCanton", "1000000");
        basket.metadatas.add(mdProdCantonProfileGDB);
        Assert.assertEquals(getBasketTotalPrice(basket), "0 CHF");
    }

    /**
     * Test an internal order with 1 product with fee. <br/>
     * provider: Canton<br/>
     * client: Canton<br/>
     * total should be 0.00 because all products are internal
     */
    @Test
    public void internalOrder1ProdWithFeeCanton() {
        login(cliCanton);
        SdiBasket basket = new SdiBasket("internalOrder1ProdWithFeeCanton", "1000000");
        basket.metadatas.add(mdProdCantonFee);
        Assert.assertEquals(getBasketTotalPrice(basket), "0 CHF");
    }

    /**
     * Test an internal order with 1 product with free. <br/>
     * provider: Canton<br/>
     * client: Canton<br/>
     * total should be 0.00 because all products are internal
     */
    @Test
    public void internalOrder1ProdWithFfeeCanton() {
        login(cliCanton);
        SdiBasket basket = new SdiBasket("internalOrder1ProdWithFfeeCanton", "1000000");
        basket.metadatas.add(mdProdCantonFee);
        Assert.assertEquals(getBasketTotalPrice(basket), "0 CHF");
    }

    /**
     * Test an internal order from thirdparty with 1 product with tarification
     * profile. <br/>
     * provider: canton<br/>
     * client: cliSansCategorie<br/>
     * thirdparty: canton<br/>
     * total should be 0.00 because all products are internal (because of
     * thirdparty)
     */
    @Test
    public void thirdPartyMakesInternalOrder1ProdWithProfileCanton() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("thirdPartyMakesInternalOrder1ProdWithProfileCanton", "1000000");
        basket.metadatas.add(mdProdCantonProfileGDB);
        basket.setThridparty(orgCanton);
        Assert.assertEquals(getBasketTotalPrice(basket), "0 CHF");
    }

    /**
     * Test an internal order from thirdparty with 1 product with fee. <br/>
     * provider: Canton<br/>
     * client: cliSansCategorie<br/>
     * thirdparty: Canton<br/>
     * total should be 0.00 because all products are internal (because of
     * thirdparty)
     */
    @Test
    public void thirdPartyMakesInternalOrder1ProdWithFeeCanton() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("thirdPartyMakesInternalOrder1ProdWithFeeCanton", "1000000");
        basket.metadatas.add(mdProdCantonFee);
        basket.setThridparty(orgCanton);
        Assert.assertEquals(getBasketTotalPrice(basket), "0 CHF");
    }
    
    
    /**
     * Test an external order with multiple free products. <br/>
     * provider: Canton + Commune<br/>
     * client: cliSansCategorie<br/>
     * thirdparty: none<br/>
     * total should be 0.00 because all products are free
     */
    @Test
    public void multipleFreeProducts() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("multipleFreeProducts", "1000000");
        basket.metadatas.add(mdProdCantonFree);
        basket.metadatas.add(mdProdCommuneFree);
        Assert.assertEquals(getBasketTotalPrice(basket), "0 CHF");
    }   
    
        /**
     * Test an external order with multiple free products. <br/>
     * provider: Canton + Commune<br/>
     * client: cliSansCategorie<br/>
     * thirdparty: none<br/>
     * total should be 0.00 because all products are free
     */
    @Test
    public void multipleFeeProducts() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("multipleFeeProducts", "1000000");
        basket.metadatas.add(mdProdCantonFee);
        basket.metadatas.add(mdProdCommuneFee);
        Assert.assertEquals(getBasketTotalPrice(basket), "- CHF");
    } 
    
 
    /**
     * Test an internal order from thirdparty with 1 product with free. <br/>
     * provider: Canton<br/>
     * client: cliMembre<br/>
     * thirdparty: Canton<br/>
     * total should be 0.00 because all products are internal (because of
     * thirdparty)
     */
    @Test
    public void thirdPartyMemberMakesInternalOrder1ProdWithFeeCanton() {
        login(cliMembre);
        SdiBasket basket = new SdiBasket("thirdPartyMakesInternalOrder1ProdWithFfeeCanton", "1000000");
        basket.metadatas.add(mdProdCantonFee);
        basket.setThridparty(orgCanton);
        Assert.assertEquals(getBasketTotalPrice(basket), "0 CHF");
    }

    // **********************************
    // Helpers
    // **********************************
    /**
     * Run after all tests of this class
     */
    @AfterClass
    public void testClean() {
        cleanCookies();
    }

    /**
     * Clear cookies for current domain.
     */
    public void cleanCookies() {
        driver.manage().deleteAllCookies();
    }

    /**
     * Writes credentials in mod login, then posts
     *
     * @param user
     */
    public void login(SdiUser user) {
        cleanCookies();
        driver.get(baseUrl);
        driver.findElement(By.id("modlgn-username")).sendKeys(user.getLogin());
        driver.findElement(By.id("modlgn-passwd")).sendKeys(user.getPassword());
        driver.findElement(By.id("login-form")).submit();
    }

    /**
     * Set a third party organism in basket
     *
     * @param org
     */
    public void selectThirdParty(SdiOrganism org) {
        getBasketPage();
        new Select(driver.findElement(By.id("thirdparty"))).selectByValue(org.getId().toString());

        //TODO Change this for a functionnal webdriverwait
        try {
            Thread.sleep(3000);
        } catch (InterruptedException ex) {
            Logger.getLogger(BasketPricingTest.class.getName()).log(Level.SEVERE, null, ex);
        }
        //Does not work
        new WebDriverWait(driver, 2).until(ExpectedConditions.presenceOfElementLocated(By.id("ordername")));

    }

    /**
     * Add all products in web basket (from "java basket")
     *
     * @param basket
     */
    public void buildBasket(SdiBasket basket) {
        for (SdiMetadata md : basket.metadatas) {
            addToBasket(md);
        }
    }

    /**
     * Adds a metadata to the basket with its id
     *
     * @param mdId
     */
    public void addToBasket(String mdId) {
        SheetPage sp = getMdSheet(mdId);
        WebElement btnAdd = driver.findElement(By.id("sdi-shop-btn-add-basket"));
        new WebDriverWait(driver, 2).until(ExpectedConditions.elementToBeClickable(btnAdd));
        btnAdd.click();
        new WebDriverWait(driver, 2).until(ExpectedConditions.visibilityOf(driver.findElement(By.id("modal-confirm"))));
    }

    /**
     * Add a metadata to basket
     *
     * @param md
     */
    public void addToBasket(SdiMetadata md) {
        addToBasket(md.getIdentifier());
    }

    /**
     * Get the metadata page with id
     *
     * @param mdId
     * @return SheetPage the MD page
     */
    public SheetPage getMdSheet(String mdId) {
        driver.get(baseUrl + "component/" + Consts.CATALOG_COMPONENT + "/" + mdId + "?view=sheet");
        return PageFactory.initElements(driver, SheetPage.class);
    }

    /**
     * Builds a fake perimeter and set the surface (used by the system) with
     * parameter Post JSON data to server and reloads page.
     *
     * @param surface
     */
    public void definePerimeter(String surface) {

        //opens popup
        driver.findElement(By.className("btn-success")).click();
        //driver.findElement(By.id("btn-perimeter1b")).click();
        if (driver instanceof JavascriptExecutor) {
            //((JavascriptExecutor) driver).executeScript("jQuery.ajax({ type: \"POST\", url: \"index.php?option=com_easysdi_shop&task=addExtentToBasket\" , data :\"item={\"id\":\"1\",\"name\":\"Free perimeter\",\"surface\":\"7007933266.164968\",\"allowedbuffer\":\"0\",\"buffer\":\"\",\"features\":\"POLYGON((-0.21203364581135184 44.92708196036781,-0.21203364581135184 45.48054434538453,1.2381616664867854 45.48054434538453,1.238161666486786 44.92708196036781,-0.21203364581135184 44.92708196036781))\"}\"}).done(function(data) { location.reload();});");
            //((JavascriptExecutor) driver).executeScript("jQuery('#ext-gen36').hide();");
            ((JavascriptExecutor) driver).executeScript(""
                    + "jQuery('#t-perimeter').val(1);"
                    + "jQuery('#t-perimetern').val('Free perimeter');"
                    + "jQuery('#t-surface').val("+surface+");"
                    + "jQuery('#allowedbuffer').val(0);"
                    + "jQuery('#buffer').val('');"
                    + "jQuery('#t-features').val('\"POLYGON((-0.21203364581135184 44.92708196036781,-0.21203364581135184 45.48054434538453,1.2381616664867854 45.48054434538453,1.238161666486786 44.92708196036781,-0.21203364581135184 44.92708196036781))\"');"
                    + "savePerimeter();");
        }
        new WebDriverWait(driver, 5).until(ExpectedConditions.textToBePresentInElementLocated(By.id("perimeter-recap"), "Surface"));

    }

    /**
     * Add all products to the basket, set the surface for the order Reads total
     * price and returns it
     *
     * Warning: Returns the string of the value, the pattern of the string
     * depends on easySDI SHOP config !
     *
     * @param basket
     * @return String total price as displayed
     */
    public String getBasketTotalPrice(SdiBasket basket) {
        buildBasket(basket);
        getBasketPage();
        definePerimeter(basket.getSurface());
        if (basket.getThridparty() != null) {
            selectThirdParty(basket.getThridparty());
        }
        WebElement we = driver.findElement(By.xpath("//table[last()]/tfoot[last()]/tr[2]/td[2]"));

        //Take screenshot
        File scrFile = ((TakesScreenshot) driver).getScreenshotAs(OutputType.FILE);
        try {
            StackTraceElement[] stacktrace = Thread.currentThread().getStackTrace();
            StackTraceElement e = stacktrace[2];//maybe this number needs to be corrected
            String callerMethodName = e.getMethodName();
            DateFormat df = new SimpleDateFormat("dd-MM-yy_HH-mm-ss");
            Date dateobj = new Date();
            FileUtils.copyFile(scrFile, new File(System.getProperty("user.dir") + "/screenshots/" + callerMethodName + "_" + df.format(dateobj) + ".png"));
        } catch (IOException ex) {
            Logger.getLogger(BasketPricingTest.class.getName()).log(Level.SEVERE, null, ex);
        }

        return we.getText();
    }

    public void getBasketPage() {
        driver.get(baseUrl + "component/" + Consts.SHOP_COMPONENT + "/?view=basket");
    }

}
