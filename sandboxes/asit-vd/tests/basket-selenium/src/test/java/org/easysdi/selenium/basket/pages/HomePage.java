package org.easysdi.selenium.basket.pages;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.CacheLookup;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.How;

/**
 * Sample page
 */
public class HomePage extends Page {

  @FindBy(how = How.TAG_NAME, using = "title")
  @CacheLookup
  public WebElement header;
  
  @FindBy(how = How.ID, using = "sdi-basket-content-display")
  @CacheLookup
  public WebElement basketitem;

  public HomePage(WebDriver webDriver) {
    super(webDriver);
  }
}
