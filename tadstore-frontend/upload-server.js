import express from "express";
import multer from "multer";
import fs from "fs";
import path from "path";

const app = express();
const uploadDir = path.resolve("src/assets/uploads");

// Asegúrate de que exista la carpeta
if (!fs.existsSync(uploadDir)) fs.mkdirSync(uploadDir, { recursive: true });

const storage = multer.diskStorage({
  destination: (req, file, cb) => cb(null, uploadDir),
  filename: (req, file, cb) => cb(null, file.originalname),
});

const upload = multer({ storage });

app.post("/upload", upload.single("file"), (req, res) => {
  res.json({ success: true, filename: req.file.originalname });
});

app.listen(4000, () => console.log("Servidor de subida en http://localhost:4000"));
