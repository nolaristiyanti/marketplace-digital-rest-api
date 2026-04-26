import { useCallback, useEffect, useMemo, useState } from "react";
import AuthContext from "./AuthContextValue";

const tokenKey = "auth_token";

const defaulAuthForm = {
  name: "",
  email: "",
  password: "",
};

export function AuthProvider({ children }) {
  const [token, setToken] = useState(
    () => localStorage.getItem(tokenKey) || null,
  );
  const [user, setUser] = useState(null);
  const [authMode, setAuthMode] = useState("login");
  const [authForm, setAuthForm] = useState(defaulAuthForm);
  const [authSubmitting, setAuthSubmitting] = useState(false);
  const [authError, setAuthError] = useState("");

  const isAuthenticated = Boolean(token);

  const getAuthHeaders = useCallback(() => {
    if (!token) return {};

    return {
      Authorization: `Bearer ${token}`,
    };
  }, [token]);

  const saveToken = useCallback((nextToken) => {
    setToken(nextToken);
    localStorage.setItem(tokenKey, nextToken);
  }, []);

  const clearToken = useCallback(() => {
    setToken("");
    setUser(null);
    localStorage.removeItem(tokenKey);
  }, []);

  useEffect(() => {
    const timerId = setTimeout(() => {
      if (!token) {
        setUser(null);
        return;
      }

      fetch("/api/auth/me", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
        .then((res) => {
          if (res.status === 401) {
            clearToken();
            return null;
          }

          if (!res.ok) {
            throw new Error("Failed to fetch user data");
          }

          return res.json();
        })
        .then((data) => {
          if (data) {
            setUser(data);
          }
        })
        .catch((err) => {
          console.error("Error fetching user data:", err);
          clearToken();
        });
    }, 0);

    return () => clearTimeout(timerId);
  }, [token, clearToken]);

  const handleAuthChange = useCallback((e) => {
    const { name, value } = e.target;
    setAuthForm((prev) => ({ ...prev, [name]: value }));
  }, []);

  const handleAuthSubmit = useCallback(
    async (e) => {
      e.preventDefault();
      setAuthSubmitting(true);
      setAuthError("");

      const url = authMode === "register" ? "/api/auth/register" : "/api/auth/login";
      const payload =
        authMode === "register"
          ? {
              name: authForm.name,
              email: authForm.email,
              password: authForm.password,
            }
          : {
              email: authForm.email,
              password: authForm.password,
            };

      try {
        const response = await fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(payload),
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || "Authentication failed");
        }

        const data = await response.json();
        const token = data.data?.token;
        const user = data.data?.user;

        if (!token) {
        throw new Error("Token tidak ditemukan di response");
        }

        saveToken(token);
        setUser(user);
      } catch (err) {
        console.error("Authentication error:", err);
        setAuthError(err.message);
      } finally {
        setAuthSubmitting(false);
      }
    },
    [
      authForm.email,
      authForm.name,
      authForm.password,
      authMode,
      saveToken,
      setAuthError,
    ],
  );

  const handleLogout = useCallback(async () => {
    try {
      await fetch("/api/auth/logout", {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          ...getAuthHeaders(),
        },
      });
    } finally {
      clearToken();
    }
  }, [clearToken, getAuthHeaders]);

  const handleToggleAuthMode = useCallback(() => {
    setAuthMode((prev) => (prev === "login" ? "register" : "login"));
    setAuthError("");
  }, []);

  const value = useMemo(
    () => ({
      authMode,
      authForm,
      authSubmitting,
      authError,
      isAuthenticated,
      user,
      getAuthHeaders,
      setAuthError,
      handleAuthChange,
      handleAuthSubmit,
      handleLogout,
      handleToggleAuthMode,
    }),
    [
      authMode,
      authForm,
      authSubmitting,
      authError,
      isAuthenticated,
      user,
      getAuthHeaders,
      handleAuthChange,
      handleAuthSubmit,
      handleLogout,
      handleToggleAuthMode,
    ],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}