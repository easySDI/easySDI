package org.easysdi.selenium.basket;

import com.google.common.base.Function;
import java.sql.Driver;
import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.TimeUnit;
import java.util.logging.Level;
import java.util.logging.Logger;
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
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.openqa.selenium.support.ui.Select;
import org.testng.annotations.AfterClass;

public class BasketPricingTest extends TestNgTestBase {

    /* CONFIG */
    //TODO: change string with config
    private final String noTotalMessage = "TO-BE-DEFINED(no total)";

    /* ******* Metadatas (products) and clients */
    private final SdiMetadata mdAsit = new SdiMetadata("MD ASITVD", "3");

    /* *** Commune
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
    private final SdiMetadata mdProdCommuneFee = new SdiMetadata("prodCommuneFee", "9");
    /**
     * Has profile GDB-com-15
     */
    private final SdiMetadata mdrodCommuneProfileGDB = new SdiMetadata("prodCommuneProfileGDB", "11");
    private final SdiMetadata mdProdCommuneFree = new SdiMetadata("prodCommuneFree", "10");

    /* *** Canton
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
    private final SdiMetadata mdProdCantonFree = new SdiMetadata("prodCantonFree", "6");
    /**
     * Has profile GDB-can-10
     */
    private final SdiMetadata mdProdCantonProfileGDB = new SdiMetadata("prodCantonProfileGDB", "5");
    /**
     * Has profile can-10
     */
    private final SdiMetadata mdProdCantonProfile = new SdiMetadata("prodCantonProfile", "8");
    private final SdiMetadata mdProdCantonFee = new SdiMetadata("prodCantonFee", "7");

    private final SdiUser cliEcole = new SdiUser("commune", "cliEcole", "987654321");
    private final SdiUser cliEtudiant = new SdiUser("commune", "cliEtudiant", "987654321");
    private final SdiUser cliNonMembre = new SdiUser("commune", "cliNonMembre", "987654321");
    private final SdiUser cliMembre = new SdiUser("commune", "cliMembre", "987654321");
    private final SdiUser cliReseaux = new SdiUser("reseaux", "cliReseaux", "987654321");
    private final SdiUser cliSansCategorie = new SdiUser("sansCategorie", "cliSansCategorie", "987654321");

    /**
     * Run before tests in this class
     */
    @BeforeClass
    public void testInit() {
        //?
    }

    /**
     * Not a real test...
     */
    @Test
    public void testAsitvdProduct() {
        login(cliCommune);
        SdiBasket basket = new SdiBasket("asitvd basket", "1000000");
        basket.metadatas.add(mdAsit);
        // a free product + 50.- for 
        Assert.assertEquals(getBasketTotalPrice(basket), "50.00 CHF");
    }

    /**
     * Test a client without category, a product with with a profile.
     */
    @Test
    public void clientWithoutCateg1ProdWithProfile() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("clientWithoutCateg1ProdWithProfile", "1000000");
        basket.metadatas.add(mdProdCantonProfile);
        // 10.- for 1sqkm + 0.8.- VAT + 25.- Fixed processing fee +  50.- platform = 85.-
        Assert.assertEquals(getBasketTotalPrice(basket), "85.80 CHF");
    }
    
    /**
     * Test a client without category, a product with with a profile.
     */
    @Test
    public void clientWithoutCateg1ProdWithProfileRounding() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("clientWithoutCateg1ProdWithProfileRounding", "10000");
        basket.metadatas.add(mdProdCantonProfile);
        // 10.- for 1sqkm + 0.8.- VAT + 25.- Fixed processing fee +  50.- platform = 85.-
        Assert.assertEquals(getBasketTotalPrice(basket), "85.80 CHF");
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
        // Should not see a total
        //TODO : check for "undefined price" string
        Assert.assertEquals(getBasketTotalPrice(basket), "0.00 CHF");
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
        Assert.assertEquals(getBasketTotalPrice(basket), "0.00 CHF");
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
        Assert.assertEquals(getBasketTotalPrice(basket), "0.00 CHF");
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
        Assert.assertEquals(getBasketTotalPrice(basket), "0.00 CHF");
    }

    /**
     * Test an internal order from thirdparty with 1 product with tarification
     * profile. <br/>
     * provider: canton<br/>
     * client: canton<br/>
     * total should be 0.00 because all products are internal (because of
     * thirdparty)
     */
    @Test
    public void thirdPartyMakesInternalOrder1ProdWithProfileCanton() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("thirdPartyMakesInternalOrder1ProdWithProfileCanton", "1000000");
        basket.metadatas.add(mdProdCantonProfileGDB);
        basket.setThridparty(orgCanton);
        Assert.assertEquals(getBasketTotalPrice(basket), "0.00 CHF");
    }

    /**
     * Test an internal order from thirdparty with 1 product with fee. <br/>
     * provider: Canton<br/>
     * client: Canton<br/>
     * total should be 0.00 because all products are internal (because of
     * thirdparty)
     */
    @Test
    public void thirdPartyMakesInternalOrder1ProdWithFeeCanton() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("thirdPartyMakesInternalOrder1ProdWithFeeCanton", "1000000");
        basket.metadatas.add(mdProdCantonFee);
        basket.setThridparty(orgCanton);
        Assert.assertEquals(getBasketTotalPrice(basket), "0.00 CHF");
    }

    /**
     * Test an internal order from thirdparty with 1 product with free. <br/>
     * provider: Canton<br/>
     * client: Canton<br/>
     * total should be 0.00 because all products are internal (because of
     * thirdparty)
     */
    @Test
    public void thirdPartyMakesInternalOrder1ProdWithFfeeCanton() {
        login(cliSansCategorie);
        SdiBasket basket = new SdiBasket("thirdPartyMakesInternalOrder1ProdWithFfeeCanton", "1000000");
        basket.metadatas.add(mdProdCantonFee);
        basket.setThridparty(orgCanton);
        Assert.assertEquals(getBasketTotalPrice(basket), "0.00 CHF");
    }

    /**
     * Run after all tests of this class
     */
    @AfterClass
    public void testClean() {
        cleanCookies();
    }

    // **********************************
    // Helpers
    // **********************************
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
    public void setThirdParty(SdiOrganism org) {
        new Select(driver.findElement(By.id("thirdparty"))).selectByValue(org.getId().toString());
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
                    + "jQuery.ajax({"
                    + " type: 'POST', "
                    + " url: 'index.php?option=com_easysdi_shop&task=addExtentToBasket' , "
                    + " data :'item={\"id\":\"1\",\"name\":\"Free perimeter\",\"surface\":\"" + surface + "\",\"allowedbuffer\":\"0\",\"buffer\":\"\",\"features\":\"POLYGON((-0.21203364581135184 44.92708196036781,-0.21203364581135184 45.48054434538453,1.2381616664867854 45.48054434538453,1.238161666486786 44.92708196036781,-0.21203364581135184 44.92708196036781))\"}'}"
                    + ").done("
                    + "    function(data) { "
                    + "        location.reload();"
                    + "    }"
                    + ");");
        }
        new WebDriverWait(driver, 2).until(ExpectedConditions.textToBePresentInElementLocated(By.id("perimeter-recap"), "Surface"));

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
        driver.get(baseUrl + "component/" + Consts.SHOP_COMPONENT + "/?view=basket");
        definePerimeter(basket.getSurface());
        WebElement we = driver.findElement(By.xpath("//table[last()]/tfoot[last()]/tr[2]/td[2]"));
        //return we.getText().split(" ")[0];
        return we.getText();
    }

}
