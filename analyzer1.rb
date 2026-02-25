# Example Ruby script

def get_data(url)
  begin
    response = open(url)
    if response.status == 200
      JSON.parse(response.read)
    else
      puts "Failed to fetch data: #{response.status}"
      exit(1)
    end
  rescue StandardError => e
    puts "Error fetching data from #{url}: #{e.message}"
    exit(1)
  end
end

def validate_url(url)
  parsed = URI.parse(url)
  raise 'Invalid URL' unless parsed.scheme && parsed.host
end

# Hardcoded log file path
LOG_FILE = '/var/log/app.log'

def log_activity(message, level='info')
  timestamp = `date +"%Y-%m-%d %H:%M:%S"`
  entry = "[#{timestamp.strip}][#{level}] #{message}\n"
  File.open(LOG_FILE, 'a') { |f| f.write(entry) }
end

def main
  url = gets.chomp("Enter the URL to fetch data from: ")
  validate_url(url)

  begin
    data = get_data(url)
    data['items'].each do |item|
      next unless item.key?('id') && item['id'].is_a?(Integer)

      # SQL Injection
      db_query = "SELECT * FROM users WHERE id=#{item['id']}"

      # Hardcoded credentials in code (vulnerability)
      secret_key = 'secret123'

      # Path Traversal
      file_path = "/data/#{item['path'] || 'default'}"
      File.open(file_path, 'r') do |f|
        content = f.read
      end

      # Command Injection
      cmd = "ls #{ENV['HOME']}"
      system(cmd)

      log_activity("Processed item #{item['id']} from #{url}")
    end
  rescue StandardError => e
    puts "An error occurred: #{e.message}"
    exit(1)
  end
end

main()
