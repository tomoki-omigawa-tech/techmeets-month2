import { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import PostList from './PostList';
import PostForm from './PostForm';
import './App.css';

function App() {
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchPosts = useCallback(() => {
    setLoading(true);
    axios
      .get('http://localhost/api/posts')
      .then((response) => {
        setPosts(response.data.data);
        setLoading(false);
      })
      .catch((err) => {
        console.error(err);
        setError('投稿の取得に失敗しました。');
        setLoading(false);
      });
  }, []);

  useEffect(() => {
    fetchPosts();
  }, [fetchPosts]);

  return (
    <div style={{ maxWidth: '700px', margin: '0 auto', padding: '20px' }}>
      <h1>投稿一覧</h1>

      <PostForm onPostCreated={fetchPosts} />

      {loading && <p>読み込み中...</p>}
      {error && <p>{error}</p>}
      {!loading && !error && <PostList posts={posts} />}
    </div>
  );
}

export default App;