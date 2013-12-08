// Colorlights project
// Adruino code to drive a breathing light based on web input

// Need to add WiFly library in this directory to Arduino desktop IDE before uploading code to device
#include "WiFly.h"

#include "credentials.h"
#include "webserver.h"

// 1 for debug mode on, 0 for off
#define DEBUG 0

#define PIN_RED 3
#define PIN_GREEN 5
#define PIN_BLUE 6

#define BREATH_LEN 16000 //adjust based on the length of a human bredth; depending on how computationally intensive you make each cycle and the power of your arduino, this number may need to be adjusted
#define BREATH_DEPTH_MIN .1

int red, green, blue;
int oldred, oldgreen, oldblue;
int cycle = 1;

Client client(webclient, 80);

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

  // set initial colors to black so we fade the light on
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
    
  // increment
  cycle++;
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
    
    // construct and send request for current color
    String request = "GET ";
    request = request + getcolorURL;
    request = request + " HTTP/1.0";
    client.println(request);
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
  return 16 * hexdigitToInt(hex[0]) + hexdigitToInt(hex[1]); // TODO: error check the outputs from digit function
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

