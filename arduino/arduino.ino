
// (Based on Ethernet's WebClient Example)

#include "WiFly.h"


#include ".gitignore/credentials.h"
//#include <string.h>

#define DEBUG 0

#define PIN_RED 3
#define PIN_GREEN 5
#define PIN_BLUE 6

#define BREATH_LEN 16000
#define BREATH_DEPTH_MIN .1
int red, green, blue;
int oldred, oldgreen, oldblue;
//int cred, cgreen, cblue; // current values for red, green, and blue
int cycle = 1;

//byte server[] = { 66, 249, 89, 104 }; // Google

//Client client(server, 80);

Client client("www.hcs.harvard.edu", 80);

void setup() {
  // setup pin outputs
  pinMode(PIN_RED, OUTPUT);
  pinMode(PIN_GREEN, OUTPUT);
  pinMode(PIN_BLUE, OUTPUT);
  
  Serial.begin(9600);

  // setup wifi
  WiFly.begin();
  
  if (!WiFly.join(ssid, passphrase)) {
    Serial.println("Association failed.");
    while (1) {
      // Hang on failure.
    }
  }  
  
  Serial.println("connecting...");

  // get an initial set of colors and save
  updateColor();
  oldred   = red;
  oldgreen = green;
  oldblue  = blue;
  
}


void loop(){
  if (cycle > BREATH_LEN) {
    cycle -= BREATH_LEN;
    updateColor();
  }
  else
    setLights(); // take set lights out of update color and call once from the loop directly
  
  if (DEBUG) {
    Serial.print("Cycle: ");
    Serial.println(cycle);
  
  }
    
  // increment and prevent var overflow
  cycle++;
  //if (cycle > BREATH_LEN)
  //  cycle -= BREATH_LEN;
  //delay(10000);
}




//----------------------------------------------------------------------------------------

void updateColor() {
  int maxRawHttp = 500;
  char rawHttp[500];
  int i = 0;
  
  // zero out the rawHTTP string
  for (int j = 0; j < maxRawHttp; j++) {
    rawHttp[j] = 0; 
  }
  
  // connect and get data
  if (client.connect()) {
    if(DEBUG)
      Serial.println("connected");
    client.println("GET /~punit/punit.org/colorlights/arduino/getColor.php HTTP/1.0");
    client.println();
  } else {
    if(DEBUG)
      Serial.println("connection failed");
  }
  delay(800);
  while (client.available()) {
    char c = client.read();
    if (DEBUG)
      Serial.print(c);
    
    // save data, unless we've already hit the max size of our HTTP input string minus 1 (for the string end char)
    if (i < maxRawHttp - 2) {
      rawHttp[i] = c;
      i++;
    }
  }
  
  rawHttp[i] = '\n'; // end string
  
  // close connection
  if (!client.connected()) {
    if (DEBUG) {
      Serial.println();
      Serial.println("disconnecting.");
    }
    client.stop();
  }

  
  // hex vars for colors
  char rhex[3];
  char ghex[3];
  char bhex[3];
  rhex[2] = '\0';
  ghex[2] = '\0';
  bhex[2] = '\0';
  
  // find start of hex color in http input
  int hexStart;
  for (hexStart = 0; hexStart < strlen(rawHttp); hexStart++)
    if (rawHttp[hexStart] == '#')
      break;
  hexStart++;
  
  // check if we never found a '#'
  /*if (hexStart >= strlen(rawHttp)) {
    rhex = "00";
    ghex = "00";
    bhex = "00";
  }*/
  
  // save hex chars
  rhex[0] = rawHttp[hexStart + 0];
  rhex[1] = rawHttp[hexStart + 1];
  ghex[0] = rawHttp[hexStart + 2];
  ghex[1] = rawHttp[hexStart + 3];
  bhex[0] = rawHttp[hexStart + 4];
  bhex[1] = rawHttp[hexStart + 5];
  
  if(DEBUG) {
    Serial.println("\nColor vals, hex:");
    Serial.println(hexStart);
    Serial.println(rhex);
    Serial.println(ghex);
    Serial.println(bhex);
    Serial.println("\nColor vals, 0-255:");
    Serial.println(hexToInt(rhex));
    Serial.println(hexToInt(ghex));
    Serial.println(hexToInt(bhex));
    Serial.println();
  }
  
  // save old colors and set new ones
  oldred   = red;
  oldgreen = green;
  oldblue  = blue;
  red   = hexToInt(rhex);
  green = hexToInt(ghex);
  blue  = hexToInt(bhex);
  
  
  // output values
  setLights();
  

  if(DEBUG)
    Serial.println("\n\n\n");
  
  
}


// sets the lights based on the intensity specified and using the gobal color vars
void setLights() {
  double intensity = getIntensity();
  
  if(DEBUG)
    Serial.println(intensity);
  
  //double avgred   = (red   * cycle + oldred   * ((BREATH_LEN - cycle)) )/(BREATH_LEN);
  //double avggreen = (green * cycle + oldgreen * ((BREATH_LEN - cycle)) )/(BREATH_LEN);
  //double avgblue  = (blue  * cycle + oldblue  * ((BREATH_LEN - cycle)) )/(BREATH_LEN);
  
  //int avgred   = (red   * cycle/100 + oldred   * ((BREATH_LEN - cycle)/100) )/(BREATH_LEN/100);
  //int avggreen = (green * cycle/100 + oldgreen * ((BREATH_LEN - cycle)/100) )/(BREATH_LEN/100);
  //int avgblue  = (blue  * cycle/100 + oldblue  * ((BREATH_LEN - cycle)/100) )/(BREATH_LEN/100);
  
  double cyclePercent = ((double)(cycle))/((double)(BREATH_LEN));
  
  double avgred   = (red   * cyclePercent + oldred   * (1-cyclePercent) );
  double avggreen = (green * cyclePercent + oldgreen * (1-cyclePercent) );
  double avgblue  = (blue  * cyclePercent + oldblue  * (1-cyclePercent) );
  
  int redtouse   = avgred   * intensity;
  int greentouse = avggreen * intensity;
  int bluetouse  = avgblue  * intensity; 
  
  
  
  analogWrite(PIN_RED, redtouse);
  analogWrite(PIN_GREEN, greentouse);
  analogWrite(PIN_BLUE, bluetouse);
  
}

double getIntensity() {
  return BREATH_DEPTH_MIN + (1-BREATH_DEPTH_MIN) * (cos(PI + PI*2*cycle/BREATH_LEN)+1)/2;
}



// converts two digit hex into integer
int hexToInt (char* hex) {
  return 16 * hexdigitToInt(hex[0]) + hexdigitToInt(hex[1]); // need to error check the outputs from digit function
}

int hexdigitToInt (char hexdigit) {
  if (hexdigit >= 48 && hexdigit <= 57) // is a number
    return hexdigit - 48;
  if (hexdigit >= 65 && hexdigit <= 70) // upercase number
    return hexdigit - 55;
  if (hexdigit >= 97 && hexdigit <= 102) // lowercase number
    return hexdigit - 87;
  
  // error
  return -1;
}

