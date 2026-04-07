const xlsx = require('xlsx'); 
const wb = xlsx.readFile('C:/Users/User/Downloads/TrafficVai/Guest post.xlsx'); 
const sheet = wb.Sheets[wb.SheetNames[0]]; 
const rows = xlsx.utils.sheet_to_json(sheet);
console.log("Headers:", Object.keys(rows[0]));
console.log("Sample:", rows[0]);
