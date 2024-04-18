#!/usr/bin/python3
# -*- coding: utf-8 -*-
import re
import sys
import base64
import tempfile
import os  
import subprocess
from flask import Flask, request

app = Flask(__name__)

@app.route('/execute_magika', methods=['POST'])
def execute_magika():
    data = request.json
    file_content_base64 = data.get('file', '')
    text = data.get('text', '')  # Retrieve the 'text' argument from JSON data

    file_content = base64.b64decode(file_content_base64)

    with tempfile.NamedTemporaryFile(delete=False) as temp_file:
        temp_file.write(file_content)

    sys.argv[1:] = [temp_file.name, text]  # Pass the 'text' argument to the script

    try:
        # Run the action and capture stdout
        process = subprocess.Popen([sys.executable, '-m', 'magika.cli.magika'] + sys.argv[1:], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        stdout, stderr = process.communicate()

        # Check for errors
        if process.returncode != 0:
            return stderr.decode('utf-8'), 500  # Return the error message and status code 500 (Internal Server Error)

        return stdout.decode('utf-8')  # Return stdout as the response
    finally:
        if 'temp_file' in locals() and hasattr(temp_file, 'name'):
            temp_file_name = temp_file.name
            temp_file.close()
            try:
                os.unlink(temp_file_name)
            except Exception as e:
                print("Error unlinking temporary file:", e)

if __name__ == '__main__':
    sys.argv[0] = re.sub(r'(-script\.pyw|\.exe)?$', '', sys.argv[0])
    app.run(debug=True, host='0.0.0.0', port=5000)
