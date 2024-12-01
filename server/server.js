const express = require('express');
const { SerialPort } = require('serialport');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();
const port = 3000;

app.use(cors());

app.use(bodyParser.json());

let serialPort;
try {
  serialPort = new SerialPort({
    path: 'COM', // Cambia 'COM10' por el puerto donde está conectado tu Arduino
    baudRate: 115200,
    autoOpen: false
  });

  serialPort.open((err) => {
    if (err) {
      console.error('Error al abrir el puerto serial:', err.message);
    } else {
      console.log('Puerto serial abierto correctamente');
    }
  });

  serialPort.on('error', (err) => {
    console.error('Error en el puerto serial:', err.message);
  });
} catch (err) {
  console.error('Error al configurar el puerto serial:', err.message);
}

app.post('/enviar-intensidad', (req, res) => {
  let { intensity } = req.body;

  if (typeof intensity === 'undefined' || isNaN(intensity)) {
    return res.status(400).json({ message: 'Intensidad no especificada o no válida' });
  }

  intensity = Math.max(0, Math.min(255, intensity));

  if (serialPort && serialPort.isOpen) {
    const command = `${intensity}\n`;
    serialPort.write(command, (err) => {
      if (err) {
        console.error('Error al enviar al Arduino:', err.message);
        return res.status(500).json({ message: 'Error al enviar al Arduino' });
      }
      console.log('Intensidad enviada:', intensity);
      res.json({ message: 'Intensidad enviada correctamente' });
    });
  } else {
    res.status(500).json({ message: 'Puerto serial cerrado o no disponible' });
  }
});

app.listen(port, () => {
  console.log(`Servidor escuchando en http://localhost:${port}`);
});

process.on('uncaughtException', (err) => {
  console.error('Error no controlado:', err.message);
});

process.on('unhandledRejection', (reason, promise) => {
  console.error('Promesa no manejada:', reason.message || reason);
});

app.get('/verificar-conexion', (req, res) => {
  if (serialPort && serialPort.isOpen) {
    res.json({ status: 'success', message: 'Conexión exitosa con el servidor y Arduino.' });
  } else {
    res.json({ status: 'error', message: 'Error de conexión con el servidor o el Arduino.' });
  }
});