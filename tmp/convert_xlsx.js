const xlsx = require('xlsx');
const fs = require('fs');
const wb = xlsx.readFile('C:/Users/User/Downloads/TrafficVai/Guest post.xlsx');
const sheet = wb.Sheets[wb.SheetNames[0]];
const csv = xlsx.utils.sheet_to_csv(sheet);
fs.writeFileSync('C:/Users/User/Downloads/TrafficVai/storage/app/guest_posts.csv', csv);
console.log('Conversion successful. File saved to storage/app/guest_posts.csv');
