int pinPWM_salida = 6;
int pinEncoder = 3;

float RPM;
float setPoint;

unsigned long previousMillis = 0;
volatile int contador = 0;
long interval = 1000;

float cv;
float cv1;

float error;
float error1;
float error2;

float kp = 0.6; //Constante de ganancia proporcional
float ki = 0.4; //Constante de ganancia integral
float kd = 0.01; //Constante de ganancia derivativa
float tiempoMuestreo = 0.1;

// ==== VARIABLES DEL FILTRO DE MEDIA MÓVIL ==== //
const int numMuestras = 5; // Tamaño de la ventana del filtro
float muestras[numMuestras]; // Arreglo para almacenar las muestras
int indice = 0; // Índice para recorrer el arreglo de muestras

void setup() {
  pinMode(pinEncoder, INPUT);
  pinMode(pinPWM_salida, OUTPUT);
  Serial.begin(115200);
  attachInterrupt(digitalPinToInterrupt(pinEncoder), interrupcion, RISING);
}

void loop() {
  unsigned long currentMillis = millis();

  if ((currentMillis - previousMillis) >= interval) {
    previousMillis = currentMillis;
    RPM = contador * (60.0 / 374.0); //Revoluciones por minuto
    contador = 0;
  }

  // ====== SET POINT ===== //
  if (Serial.available() > 0) {
    setPoint = Serial.parseFloat(); // Leer el set point desde el monitor serial
    setPoint = constrain(setPoint, 0, 255);
    Serial.print("Nuevo Set Point: ");
    Serial.println(setPoint);
  }

  if (setPoint == 0) {
    analogWrite(pinPWM_salida, 0); // Detener el motor si el set point es 0
  } else {
    error = setPoint - getFilteredRPM(); // Obtener el error utilizando el valor filtrado de RPM

    // ====== ECUACIÓN DE DIFERENCIAS ====== //
    cv = cv1 + (kp + kd / tiempoMuestreo) * error + (-kp + ki * tiempoMuestreo - 2 * kd / tiempoMuestreo) * error1 + (kd / tiempoMuestreo) * error2;
    cv1 = cv;
    error2 = error1;
    error1 = error;

    //"Saturando la salida del PID..."
    if (cv > 255.0) {
      cv = 255.0;
    }

    if (cv < 0.0) {
      cv = 0.0;
    }

    analogWrite(pinPWM_salida, cv * (255.0 / 500.0));
  }

  // ====== IMPRESIONES ====== //
  Serial.print(setPoint); // Enviar el valor de setPoint
  Serial.print(" ==> SetPoint || RPM ==> ");
  Serial.print(getFilteredRPM()); // Enviar el valor de RPM filtrado
  Serial.print(" , ");
  Serial.print(error);
  Serial.println(); // Agregar una nueva línea al final
  delay(200); //Tiempo entre cada impresión de medida
}

// ====== FUNCIONES ====== //
void interrupcion() {
  contador++;
}

float getFilteredRPM() {
  float suma = 0;
  muestras[indice] = RPM; // Almacenar la nueva muestra en el arreglo
  indice = (indice + 1) % numMuestras; // Avanzar el índice y volver al principio si se alcanza el final del arreglo

  // Calcular la suma de todas las muestras
  for (int i = 0; i < numMuestras; i++) {
    suma += muestras[i];
  }

  // Calcular el promedio de las muestras
  float promedio = suma / numMuestras;
  return promedio;
}