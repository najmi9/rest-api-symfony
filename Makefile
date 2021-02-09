jwt:
	curl -X POST -H "Content-Type: application/json" http://localhost:8000/api/auth/login -d '{"username":"johndoe","password":"test"}'
